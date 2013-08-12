import re

def process(inputFile, outputFile):
    fin = open(inputFile)
    fout = open(outputFile, "w")

    coordinatePattern = re.compile(r"-?\d{1,3}\.\d{6},\s*-?\d{1,3}\.\d{6}")
    
    isHead = True
    formerUDID = ""
    curCoordinates = ""
    
    for line in fin:
        
        if isHead:
            #fout.write("id;category;store\n")
            isHead = False
            continue

        strs = line.strip().split(',', 3)

        if len(strs[3]) != 0:
            curCoordinates = ""
            coordinates = coordinatePattern.search(strs[3])
            if coordinates:
                curCoordinates = coordinates.group().replace(' ', '')
        elif not cmp(strs[0], formerUDID) and len(curCoordinates) != 0 and len(strs[2]) != 0:
            fout.write(strs[0] + ";" + strs[1] + ";" + strs[2] + ";" + curCoordinates + "\n")
        elif cmp(strs[0], formerUDID):
            curCoordinates = ""

        formerUDID = strs[0]

    fout.close()
    fin.close()

process(r"1375070340574_28.csv", r"udid-cate-gps-sh-0728.csv")
            
