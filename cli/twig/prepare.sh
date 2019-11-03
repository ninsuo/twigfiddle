
# Uncompressing supported Twig versions
for file in compressed/*;
do
    echo "uncompress ${file}...";
    tar xzfC $file uncompressed/;
done

# Uncompressing supported Twig extension versions
for file in extension/compressed/*;
do
    echo "uncompress ${file}...";
    tar xzfC $file extension/uncompressed/;
done

# Applying security patch
php security.php


