import xml.etree.ElementTree as ET
import configparser
from collections import defaultdict

divisions = {}

class Division:
    def __init__(self, name):
        self.name = name
        self.matches = []
        self.teams = []
        
    def addMatch(self, team1, team2):
        self.matches.append(Match(team1, team2))
        if team1 not in self.teams:
            self.teams.append(team1)
        if team2 not in self.teams:
            self.teams.append(team2)
        
    def findTeamOfMember(self, memberName):
        for team in self.teams:
            if team.hasMember(memberName):
                return team
        return None
        
    def findMember(self, memberName):
        team = self.findTeamOfMember(memberName)
        if team == None:
            return None
        member = team.findMember(memberName)
        return member
        
    def getResult(self):
        results = []
        results.append(self.name)
        for res in matches:
            results.append(res.getResult())
        return results
                

class Match:
    def __init__(self, team1, team2):
        self.team1 = team1
        self.team2 = team2     
        
class Team:
    def __init__(self, name):
        self.name = name
        self.members = {}
        
    def addMembers(self, teamList):
        names = teamList.split(',')
        for name in names:
            self.members[name] = Person(name)
            
    def hasMember(self, name):
        return name in self.members
        
    def findMember(self, name):
        if name in self.members:
            return self.members[name]
        else:
            return None           
            
    def getStatus(self, name):
        if self.hasMember(name):
            return self.members[name].status
        else:
            return None
            
    def getScore(self):
        score = 0
        for member in self.members.values():
            score = score + member.score
        return score
            
    def getPlayers(self):
        str =""
        for name in self.members:
            str = str + name + ","
        return str[:-1]
            

class Person:
    def __init__(self, name):
        self.name = name
        self.score = 0
        self.status = "DNS"
        self.runningTime = -1
    
    def setTime(self, runningTime, status):
        self.status = status
        self.runningTime = int(runningTime)
        
    def textTime(self):
        mins = self.runningTime//60
        secs = self.runningTime - (60*mins)
        return str(mins) + ":" + str(secs)
        
    def getResult(self):
        if self.runningTime > 0:
            return "<td>" + self.name + "</td><td>" + self.textTime() + "</td><td>" + str(self.score) + "</td>"
        else:    
            return "<td>" + self.name + "</td><td>" + self.status + "</td><td>" + str(self.score)+ "</td>"

def is_number(s):
    try:
        float(s)
        return True
    except ValueError:
        return False
        
def get_team(string, index):
    teams = string.split(',')
    return Team(teams[index])
    
def normalise(string):
    string = string.replace(" ", "_")
    return string

def read_teams():
    config = configparser.ConfigParser()
    config.read('teams.ini')
    divnames = config.sections()
    for div in divnames:
        divisions[div] = Division(div)
        count = int(config[div]['MatchCount'])
        if count != 0:
            for x in range(0, count):
                teamnames = config[div]["Match" + str(x+1)].split(",")
                team1 = Team(teamnames[0])
                team2 = Team(teamnames[1])
                divisions[div].addMatch(team1, team2)
                members1 = config[div][normalise(team1.name)]
                team1.addMembers(config[div][normalise(team1.name)])
                team2.addMembers(config[div][normalise(team2.name)])
            
def print_results():
    print("<html>")
    print("<head>")
    print('<link href="https://fonts.googleapis.com/css?family=Permanent+Marker|Roboto&display=swap" rel="stylesheet">')
    print('<style>')
    print("body {font-family: 'Permanent+Marker', cursive; font-size: 48px; color:darkgreen}")
    print('</style>')
    print('</head>')
    print('<body>')
    for name,div in divisions.items():
        print ("<H3>" + name + "</H3>")

        for match in div.matches:
            print ("<p>" + match.team1.name + ": " + str(match.team1.getScore()) + " v " + match.team2.name + ": " + str(match.team2.getScore()) + "</p>")
            print('<table><tr><th scope="col">Runner</th><th scope="col">Time</th><th scope="col">Points</th><th scope="col">Runner</th><th scope="col">Time</th><th scope="col">Points</th></tr>')
            team1s = match.team1.getPlayers().split(',')
            team2s = match.team2.getPlayers().split(',')
            index = 0
            while index < max(len(team1s),len(team2s)):
                if index < len(team1s):
                    mem = match.team1.findMember(team1s[index])
                    if mem == None:
                        print("No match for " + team1s[index])
                    res1 = mem.getResult()
                else:
                    res1 = "n.a.  -  0"
                if index < len(team2s):
                    mem = match.team2.findMember(team2s[index])
                    res2 = mem.getResult()
                else:
                    res2 = "n.a.  -  0"
                print("<tr>" + res1 + res2 + "</tr>")
                index = index + 1
            print("</table>")
    print('</body>')
    print('</html>')

         

def process_person(DivNameText, PersonResult, ResultLookup):
    Person = PersonResult.find("Person")
    if not Person:
        return
    Name = Person.find("Name")
    if not Name:
        return
    Family = Name.find("Family")
    if Family == None:
        return
    Given = Name.find("Given")
    if Given == None:
        return

    Result = PersonResult.find("Result")
    if Result == None:
        return
    Status = Result.find("Status")
    if Status == None:
        return
    if Status.text == "OK":
        RunningTime = Result.find("Time")
        if RunningTime == None:
            return
        ResultLookup[Given.text + " " + Family.text] = (RunningTime.text, Status.text)
    else:
        ResultLookup[Given.text + " " + Family.text] = ("-1","-1", Status.text)
        
             
def process_division(DivNameText, ClassResult):
    ResultLookup = {}
    PersonResults = ClassResult.findall('PersonResult')
    for PersonResult in PersonResults:
        process_person(DivNameText, PersonResult, ResultLookup)

    for personName, res in ResultLookup.items():
        if DivNameText in divisions.keys():
            member = divisions[DivNameText].findMember(personName)
            if member != None:
                member.setTime(res[0], res[1])
        else:
            print(DivNameText + " is not a configured division")
            
# Calculate the scores for each runner
    
    for match in divisions[DivNameText].matches:
        scorers = defaultdict(list)
        for member in match.team1.members.values():
            if member.runningTime > -1:
                scorers[member.runningTime].append(member)
        for member in match.team2.members.values():
            if member.runningTime > -1:
                scorers[member.runningTime].append(member)
        score = 10
        for key in sorted(scorers.keys()):
            for member in scorers[key]:
                member.score = score
            score = score - len(scorers[key])
        

        
        
# Read the team info from INI file
read_teams()

# Read the results from IOF3 XML
input_file = "ResultsIOF3.xml"
with open (input_file, "r") as myfile:
    data=myfile.readlines()

xml = list()
for line in data:
    if line.startswith("<ResultList "):
        xml.append("<ResultList>")
    else:
        xml.append(line)
        
with open(input_file, 'w') as f:
    for item in xml:
        f.write(item)
        
root = ET.parse(input_file).getroot()

ClassResults = root.findall('ClassResult')
for ClassResult in ClassResults: 
    Class = ClassResult.find('Class')
    if Class:
        Name = Class.find('Name')
        if (Name.text.startswith("Division ")):
            process_division(Name.text, ClassResult)
            
print_results()




