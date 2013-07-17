fin = open("viewCate.txt", "r")
fout= open("result-ct.txt", "w")
curCluster = "cluster0"
count = 0
cat = {}
for line in fin:
    strs = line.split("\t")
    if len(strs) != 3 or len(strs[1].strip()) == 0 or len(strs[2].strip()) == 0:
        continue
    if curCluster == strs[0]:
        count += 1
        if not strs[2] in cat:
            cat[strs[2]] = 1
        else:
            cat[strs[2]] += 1
    else:
        for ct in sorted(cat.items(), key=lambda d : d[1], reverse=True):
            fout.write(curCluster + "\t" + ct[0].strip() + "\t" + str(ct[1]/count) + "\n")
        curCluster = strs[0]
        count = 0
        cat.clear()
fout.close()
fin.close()
