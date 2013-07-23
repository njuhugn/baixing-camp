
import re

jingdu = [181.0, -1.0]
weidu = [181.0, -1.0]
pattern = re.compile(r"\d+.\d+")
fin = open("gps.txt", "r")
fout = open("gps-origin.txt", "w")
for line in fin:
    strs = line.split("\t")
    if len(strs) != 2:
        continue
    latitude = pattern.findall(strs[1])
    if len(latitude) == 2:
        fout.write(strs[0] + "\t" + latitude[0] + "\t" + latitude[1] + "\n")
        l1 = float(latitude[0])
        l2 = float(latitude[1])
        if l1 < 0.0 or l1 > 180.0 or l2 < 0.0 or l2 > 180.0:
            continue
        if jingdu[0] > l1:
            jingdu[0] = l1
        if jingdu[1] < l1:
            jingdu[1] = l1
        if weidu[0] > l2:
            weidu[0] = l2
        if weidu[1] < l2:
            weidu[1] = l2
fout.close()
fin.close()
print(jingdu)
print(weidu)

fout = open("gps.arff", "w")
fout.write("@relation gps\n")
fout.write("@attribute udid string\n")
fout.write("@attribute jingdu real\n")
fout.write("@attribute weidu real\n")
fout.write("\n@data\n\n")

fin = open("gps.txt", "r")
for line in fin:
    strs = line.split("\t")
    latitude = pattern.findall(strs[1])
    if len(latitude) == 2:
        l1 = float(latitude[0])
        l2 = float(latitude[1])
        if l1 < 0.0 or l1 > 180.0 or l2 < 0.0 or l2 > 180.0:
            continue
        r1 = (l1 - jingdu[0]) / (jingdu[1] - jingdu[0])
        r2 = (l2 - weidu[0]) / (weidu[1] - weidu[0])
        if r1 == 1 and r2 == 1:
            continue
        fout.write(str(strs[0]) + ",")
        fout.write(str(r1) + ",")
        fout.write(str(r2) + "\n")

print("done")
fout.close()
fin.close()
                   
                    
