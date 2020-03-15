import xml.etree.ElementTree as ET
import configparser

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
        for each team in self.teams:
            if team.hasMember(memberName):
                return team
        return None
        
    def findMember(self, memberName):
        team = self.findTeamOfMember(memberName)
        if team == None:
            return None
        member = team.findMember(memberName)
                
    def setResult(self, memberName, result):
        team = 

class Match:
    def __init__(self, team1, team2)
        self.team1 = team1
        self.team2 = team2
        
    def getResult:
        return team1.name + " " + team1.getScore() + " " + team2.name + " " + team2.getScore()
        
    def setResult(self, name, result):

class Team:
    def __init__(self, name):
        self.name = name
        self.members = []
        
    def addMembers(self, teamList)
        self.members = teamlist.split(',')
        for name in names:
            self.members[name] = Person(name)
            
    def hasMember(self, name):
        return name in self.members
        
    def findMember(self, name):
        if name in self.members:
            return self.members[name]
        else:
            return None
        
    def setScore(self, name, score):
        if self.hasMember(name):
            self.members[name].setScore(score)
            
    def getScore(self):
        score = 0
        for member in self.members:
            score = score + member.score
            return score
            

class Person:
    def __init__(self, name):
        self.name = name
        self.score = 0
    
    def setScore(self, score)
        self.score = 0


def get_team(string, index):
    teams = string.split(',')
    return teams[index]
    

def read_teams:
    config = configparser.ConfigParser()
    config.read('teams.ini')
    divisions = config.sections()
    for div in divisions:
        count = config[div]['MatchCount']
        for x in range(1, count):
            match[x] = (get_team(config[div]["Match" + str(x)],0), get_team(config[div]["Match" + str(x)],1)
            

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
        TimeBehind = Result.find("TimeBehind")
        if TimeBehind == None:
            return
        ResultLookup[Given.text + " " + Family.text] = TimeBehind.text
    else:
        ResultLookup[Given.text + " " + Family.text] = Status.text
        
             
def process_division(DivNameText, ClassResult):
    print(DivNameText)

    ResultLookup = {}
    PersonResults = ClassResult.findall('PersonResult')
    for PersonResult in PersonResults:
        process_person(DivNameText, PersonResult, ResultLookup)
    


# instead of ET.fromstring(xml)
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
            
            



