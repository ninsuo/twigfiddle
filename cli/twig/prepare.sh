
# Uncompressing supported Twig versions
for file in compressed/*;
do
    echo "uncompress ${file}...";
    tar xzfC $file uncompressed/;
done

# Applysing security patch
php security.php

# Compilling all C extensions
for c_ext in uncompressed/*/ext/twig;
do
    if ! [ -f "$c_ext/modules/twig.so" ]
    then
        echo "compile ${c_ext}...";
        (
            cd $c_ext;
            phpize;
            ./configure;
            make
        )
    fi
done

