#!/bin/sh

echo "Creating JASmine distribution packages"
echo

while [ -z $VERSION ]
do
	echo "Enter this release's version number (x.y.z):"
	read VERSION
done

## TEST !
#sed s'/\$VERSION:\s(\S+)\$/\$VERSION: $VERSION\$/' CHANGELOG-0.0.3

echo -n "Removing old tarballs: "
rm -f JASmine-Web-$VERSION.tar.bz2 JASmine-Backend-$VERSION.tar.bz2 JASmine-MySQL-$VERSION.tar.bz2
echo "Done."

echo -n "Creating copies: "
# Using ending "/" so symlinking doesn't annoy us.
cp -r JASmine-Web/ JASmine-Web-$VERSION
cp -r JASmine-Backend/ JASmine-Backend-$VERSION
cp -r JASmine-MySQL/ JASmine-MySQL-$VERSION
echo "Done."

echo -n "Creating MANIFESTs: "
find JASmine-Web-$VERSION | grep -v .svn > JASmine-Web-$VERSION/MANIFEST
find JASmine-Backend-$VERSION | grep -v .svn > JASmine-Backend-$VERSION/MANIFEST
find JASmine-MySQL-$VERSION | grep -v .svn > JASmine-MySQL-$VERSION/MANIFEST
echo "Done."

echo -n "Creating tarballs: "
tar cjf JASmine-Web-$VERSION.tar.bz2 --exclude=.svn --no-wildcards JASmine-Web-$VERSION
tar cjf JASmine-Backend-$VERSION.tar.bz2 --exclude=.svn --no-wildcards JASmine-Backend-$VERSION
tar cjf JASmine-MySQL-$VERSION.tar.bz2 --exclude=.svn --no-wildcards JASmine-MySQL-$VERSION
echo "Done."

echo -n "Removing copies: "
rm -rf JASmine-Web-$VERSION JASmine-Backend-$VERSION JASmine-MySQL-$VERSION
echo "Done."

echo
ls -l --color
echo
