#include "ciof3xmlcontenthandler.h"
#include "cresult.h"
#include <QDebug>

CIof3XmlContentHandler::CIof3XmlContentHandler(QVector <CResult*>& a_Results):
    QXmlDefaultHandler(), m_RaceTime(0),m_RacePos(0),m_Results(a_Results)
{
}


CIof3XmlContentHandler::~CIof3XmlContentHandler()
{

}

bool CIof3XmlContentHandler::endElement(const QString& , const QString &localName, const QString &)
{
    if (localName != m_Elements.last() && m_Elements.size() > 0)
        qDebug() << "ending element" << localName << "expected" << m_Elements.last();
    else
        m_Elements.pop_back();

    if (localName == "PersonResult")
        AddPerson();
    return true;
}

bool CIof3XmlContentHandler::characters(const QString &ch)
{
    if (m_Elements.size() > 1 && m_Elements.last() == "Time" && m_Elements[m_Elements.size()-2] == "Result")
        m_RaceTime = ch.toInt();
    if (m_Elements.size() > 1 && m_Elements.last() == "Name" && m_Elements[m_Elements.size()-2] == "Class")
        m_Class = ch;
    if (m_Elements.size() > 2 && m_Elements.last() == "Family" && m_Elements[m_Elements.size()-2] == "Name" && m_Elements[m_Elements.size()-3] == "Person")
        m_SName = ch;
    if (m_Elements.size() > 2 && m_Elements.last() == "Given" && m_Elements[m_Elements.size()-2] == "Name" && m_Elements[m_Elements.size()-3] == "Person")
        m_FName = ch;
    if (m_Elements.size() > 1 && m_Elements.last() == "Name" && m_Elements[m_Elements.size()-2] == "Organisation")
    {
        m_Club = ch;
        m_Club.replace("Orienteering Club","OC");
    }

    if (m_Elements.size() > 1 && m_Elements.last() == "Position" && m_Elements[m_Elements.size()-2] == "Result")
         m_RacePos = ch.toInt();
    if (m_Elements.size() > 1 && m_Elements.last() == "Status" && m_Elements[m_Elements.size()-2] == "Result")
    {
        if (ch != "OK")
        {
            m_RacePos = 0;
        }
        m_RaceStatus = ch;
    }
    return true;
}

bool CIof3XmlContentHandler::startElement(const QString & , const QString & localName,
                const QString &, const QXmlAttributes & atts )
{
    m_Attributes = atts;

    m_Elements.append(localName);
    return true;
}


void CIof3XmlContentHandler::AddPerson()
{
    CResult* runner = new CResult();
    runner->setName(m_FName + " " + m_SName);
    runner->setClub(m_Club);
    runner->setStatus(m_RaceStatus);
    runner->setPosition(m_RacePos);
    runner->setTimeS(m_RaceTime);
    runner->setClass(m_Class);

    m_Results.append(runner);
    m_FName.clear();
    m_SName.clear();
    m_Club.clear();
    m_RacePos = 0;
    m_RaceTime = 0;
    m_RaceStatus.clear();
}

