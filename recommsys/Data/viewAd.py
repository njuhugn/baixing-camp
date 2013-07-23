fin = open("viewAd.txt", "r")
fo1 = open("result-ad.txt", "w")
fo2 = open("result-ct.txt", "w")
curCluster = "cluster0"
ads = {}
cat = {}
for line in fin:
    strs = line.split("\t")
    if len(strs) != 3 or len(strs[1].strip()) == 0 or len(strs[2].strip()) == 0:
        continue
    if curCluster == strs[0]:
        if not strs[1] in ads:
            ads[strs[1]] = 1
        else:
            ads[strs[1]] += 1
        if not strs[2] in cat:
            cat[strs[2]] = 1
        else:
            cat[strs[2]] += 1
    else:
        for ad in sorted(ads.items(), key=lambda d : d[1], reverse=True):
            fo1.write(curCluster + "\t" + ad[0].strip() + "\t" + str(ad[1]) + "\n")
        for ct in sorted(cat.items(), key=lambda d : d[1], reverse=True):
            fo2.write(curCluster + "\t" + ct[0].strip() + "\t" + str(ct[1]/len(ads)) + "\n")
        curCluster = strs[0]
        ads.clear()
        cat.clear()
fo1.close()
fo2.close()
fin.close()
