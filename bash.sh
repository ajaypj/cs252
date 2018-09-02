# path="/mnt/D/Sem5/CS252/Assignments/a1/cs252temp/recv/fold/"
> view.html
rm received_files/*.jpg
./client 3 cats 2 dogs 3 trucks 1 cars
echo `cat start` >> view.html

#cnt=0
#if [ "$1" -ne 0 ]; then
#	((cnt++))
#fi
#if [ "$2" -ne 0 ]; then
#	((cnt++))
#fi
#if [ "$3" -ne 0 ]; then#
#	((cnt++))
#fi
#if [ "$4" -ne 0 ]; then
#	((cnt++))
#fi

#path="/mnt/D/Sem5/CS252/Assignments/a1/cs252temp/makehtml/images/cars/"
path="/home/prithviraj/Desktop/cs252/cs251temp/received_files/"

for filepath in $(ls $path); do
	echo "<img src=\""$path$filepath"\">" >> view.html
	#echo "<img src=\""$path$filepath"\">"
done

echo `cat end` >> view.html
