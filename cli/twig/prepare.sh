
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
    echo "compile ${c_ext}...";
    (
        cd $c_ext;
        phpize;
        ./configure;
        make
    )
done

