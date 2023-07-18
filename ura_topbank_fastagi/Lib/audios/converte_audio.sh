mkdir convertidos
for i in *.wav; do sox "$i" -t wav -b 16 -r 8000 -c 1  "convertidos/${i%%.wav}.wav"; done
