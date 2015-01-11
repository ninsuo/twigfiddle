<?php

namespace Fuz\AppBundle\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

class Paginator
{

    protected $logger;
    protected $session;
    protected $user;

    public function __construct(LoggerInterface $logger, Session $session, TokenStorage $token)
    {
        $this->logger = $logger;
        $this->session = $session;
        if (!is_null($token->getToken()))
        {
            $this->user = $token->getToken();
        }
    }

    public function getDefaultConfiguration()
    {
        return array (
                'session_key' => 'pagination',
                'preference_prefix' => 'pagination.',
                'page_query_string' => 'page',
                'per_page_query_string' => 'per_page',
                'per_page_list' => array (10, 25, 50, 75, 100),
                'displayed_links' => 7,
                'session' => array (
                        'current_page' => 1,
                        'current_per_page' => 10,
                ),
        );
    }

    protected function getInternalConfiguration()
    {
        return array (
                'count_results' => -1,
                'count_pages' => -1,
                'page_list' => array (),
                'display_first' => null,
                'display_last' => null,
                'dots_first' => null,
                'dots_last' => null,
        );
    }

    public function paginate(Request $request, QueryBuilder $query, $count, array $config = array ())
    {
        $options = array_merge($this->getDefaultConfiguration(), $config, $this->getInternalConfiguration());
        $options['session'] = array_merge($options['session'], $this->session->get($options['session_key'], array()));
        $sess = &$options['session'];

        $options['count_results'] = $count;

        $sess['current_per_page'] = $request->query->get($options['per_page_query_string'], $sess['current_per_page']);
        if (!in_array($sess['current_per_page'], $options['per_page_list']))
        {
            $sess['current_per_page'] = reset($options['per_page_list']);
        }

        $sess['current_page'] = $request->query->get($options['page_query_string'], $sess['current_page']);
        if ($sess['current_page'] < 1)
        {
            $sess['current_page'] = 1;
        }
        $options['count_pages'] = floor($options['count_results'] / $sess['current_per_page']);
        if (($options['count_results'] == 0) || ($options['count_results'] % $sess['current_per_page']) > 0)
        {
            $options['count_pages'] += 1;
        }
        if ($sess['current_page'] > $options['count_pages'])
        {
            $sess['current_page'] = 1;
        }

        $query->setFirstResult(($sess['current_page'] - 1) * $sess['current_per_page']);
        $query->setMaxResults($sess['current_per_page']);

        $this->session->set($options['session_key'], $sess);

        return $this->createContext($options);
    }

    protected function createContext(array $options)
    {
        if ($options['session']['current_per_page'] > 0)
        {
            if ($options['count_results'] <= $options['session']['current_per_page'])
            {
                $options['page_list'][] = 1;
            }
            else
            {
                $options['page_list'] = range(1, ceil($options['count_results'] / $options['session']['current_per_page']));
                if (($options['displayed_links'] = floor($options['displayed_links'] / 2) * 2 + 1) >= 1)
                {
                    $min = min(
                       count($options['page_list']) - $options['displayed_links'],
                       intval($options['session']['current_page']) - ceil($options['displayed_links'] / 2)
                    );
                    $options['page_list'] = array_slice($options['page_list'], max(0, $min), $options['displayed_links']);
                }
            }
        }

        $options['display_first'] = count($options['page_list']) > 1 && !in_array(1, $options['page_list']);
        $options['display_last'] = count($options['page_list']) > 1 && !in_array($options['count_pages'], $options['page_list']);
        $options['dots_first'] = count($options['page_list']) > 1 && !in_array(2, $options['page_list']);
        $options['dots_last'] = count($options['page_list']) > 1 && !in_array($options['count_pages'] - 1, $options['page_list']);

        return $options;
    }

    public function reset($key)
    {
        $this->session->remove($key);
    }

}
