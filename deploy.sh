echo "please enter commit info："

read msg 

git pull origin master

git add .
git commit -a -m "$msg"
git push -u origin master
