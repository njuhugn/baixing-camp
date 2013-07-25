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
            fout.write("id;category;store\n")
            isHead = False
            continue

        strs = line.strip().split(',', 2)

        if len(strs[2]) != 0:
            curCoordinates = ""
            coordinates = coordinatePattern.search(strs[2])
            if coordinates:
                curCoordinates = coordinates.group().replace(' ', '')
        elif not cmp(strs[0], formerUDID) and len(curCoordinates) != 0 and len(strs[1]) != 0:
            fout.write(line.strip().replace(",", ";") + curCoordinates + "\n")
        elif cmp(strs[0], formerUDID):
            curCoordinates = ""

        formerUDID = strs[0]

    fout.close()
    fin.close()

process(r"udid-cate-gps-0724.csv", r"E:\apache-solr-3.6.2\example\exampledocs\udid-cate-gps-shanghai-0724.csv")
            
