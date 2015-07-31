for file in compressed/*;
do
    echo "${file}...";
    tar xzfC $file uncompressed/;
done
php security.php