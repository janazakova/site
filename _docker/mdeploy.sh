#!/bin/bash
set -e

cd /code/_site/
mkdir /code/tmp/
mv /code/_site/~xpopelka/* /code/tmp/
cp -r /code/tmp/* /code/_site/
filesChanged=$(find . -type f)
if [ ${#filesChanged[@]} -eq 0 ]; then
    echo "No files to update"
else
    for f in $filesChanged
    do
        #do not upload these files that aren't necessary to the site
        if [ "$f" != ".travis.yml" ] && [ "$f" != "deploy.sh" ] && [ "$f" != "test.js" ] && [ "$f" != "package.json" ]
        then
            echo "Uploading $f"
            curl -s --insecure --ftp-create-dirs -T $f -u $MFTP_USER:$MFTP_PASS $MFTP_TARGET/$f
            if [ $? -ne 0 ]; then
              echo "Could not upload file" >&2
              exit 1
            fi
        fi
    done
fi
