echo "please enter commit info："

read msg

git pull origin $1

git add .
git commit -a -m "$msg"
git push -u origin $1
