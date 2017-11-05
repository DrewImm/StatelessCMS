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

echo "Version: "$VERSION;
echo "Build Name: "$BUILDNAME;

# Remove old temp directory
echo -e;
echo "Cleaning build directory...";

rm -rf temp build;
mkdir -p temp;

# Zip
echo "Creating archives...";

zip -r temp/$BUILDNAME.zip lib autoload.php >> build.log
tar -zcvf temp/$BUILDNAME.tar.gz lib autoload.php >> build.log

# Move to build
echo "Completing build...";
mv temp build;

# Remove build log
echo "Cleaning up...";
rm -rf build.log

# Done
echo "Build Complete.";