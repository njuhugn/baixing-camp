import re

coordinates = re.compile(r"\d+.\d+")
maxmin = [181.0, -1.0]

def process(inputFile, outputFile):
    fin = open(inputFile)
    fout = open(outputFile, "w")
    
    isHead = True
    udid = ""
    coordinate = []
    categories = []
    latitude = 0.0
    longitude = 0.0
    
    for line in fin:
        
        if isHead:
            isHead = False
            continue

        curId = line.split(',')[0]
        curCoordinate = []
        curCategory = ""
        if "GPS" in line:
            strs = line.split('"')
            if len(strs) >= 2:
                coordinate = coordinates.findall(strs[1])
                if len(coordinate) >= 2:
                    curCoordinate = coordinate[:2]
        else:
            strs = line.split(',')
            curCategory = strs[2]
            #curCategory = strs[9]
        
        if cmp(udid, curId):
            if latitude != 0.0 and longtitude != 0.0 and len(categories) != 0:
                result = str(latitude) + "," + str(longtitude) + "," + ','.join(list(set(categories)))
                #print result
                fout.write(result + "\n")
            categories = []
            latitude = longtitude = 0.0
            udid = curId

        if cmp(curCategory, ""):
            categories.append(curCategory)
        if len(curCoordinate) == 2:
            latitude = curCoordinate[0]
            longtitude = curCoordinate[1]

    fout.close()
    fin.close()

#process(r"udid-category-gps.csv", r"gps-category.txt")
            
