#Production deployment

1. SSH into prod
2. `git clone` or `git pull` master branch
3. `cd` into project root
4. Run `php bin/build.php tag` where *tag* is the tag or branch name to deploy, default is master. This will build a tarball like `/tmp/20160125204920-master.tar.gz`. Watch for the instructions
5. Deploy the build: `sudo tar -zxf /tmp/20160125204920-master.tar.gz -C /www/release/` - this will create a corresponding release folder
6. Run all the necessary schema updates (TBD)
7. Switch the symlink: `sudo ln -sfT /www/release/20160125204920-master /www/current`
