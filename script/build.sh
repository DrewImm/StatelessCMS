# Pull version
file=$PWD/autoload.php;
VERSION="";

while IFS=' ' read -r f1 f2 f3
do
    if [ "$f2" == "@version" ]
    then
        VERSION=$f3;
    fi
done <"$file"

# Create build name
BUILDNAME="StatelessCMS-"$VERSION;

echo "Version: "$VERSION
echo "Build Name: "$BUILDNAME

# Remove old temp directory
echo -e
echo "Cleaning build directory..."

rm -rf temp
mkdir -p temp

# Move files
cp -rf lib temp/StatelessCMS
cp autoload.php temp/StatelessCMS.php

# Zip
echo "Creating archives..."
cd temp

zip -r $BUILDNAME.zip StatelessCMS.php StatelessCMS >> build.log
tar -zcvf $BUILDNAME.tar.gz StatelessCMS.php StatelessCMS >> build.log

cd ..

# Move to build
echo "Completing build..."
mv temp/$BUILDNAME.zip build
mv temp/$BUILDNAME.tar.gz build

# Remove build log
echo "Cleaning up..."
rm -rf build.log

# Done
echo "Build Complete."