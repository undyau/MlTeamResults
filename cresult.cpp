#include "cresult.h"
#include <QDebug>

CResult::CResult():m_Scorer(false)
{
}

bool CResult::Scorer() const
{
    return m_Scorer;
}

void CResult::setScorer(bool Scorer)
{
    m_Scorer = Scorer;
}

QString CResult::Status() const
{
    return m_Status;
}

int CResult::TimeS() const
{
    return m_TimeS;
}

void CResult::setTimeS(int TimeS)
{
    m_TimeS = TimeS;
}

QString CResult::Time()
{
    uint min, sec(m_TimeS);
    min = m_TimeS /60;
    sec -= min*60;
    return QString("%1:%2").arg(min).arg(sec,2,10,QChar('0'));
}

QString CResult::ResultHtml()
{
    QString ret;

    if (m_Status == "OK")
    {
        ret += "<tr>";
        ret += QString("<td>%1%2</td><td>%3</td><td>%4</td><td>%5</td>")
                .arg(m_Position).arg(Scorer() ? QString("*") : QString("")).arg(m_Name).arg(m_Club).arg(Time());
        ret += "</tr>\r\n";
    }
    else
        ret = QString("<tr><td></td><td>%2</td><td>%3</td><td>%4</td></tr>\r\n")
                .arg(m_Name).arg(m_Club).arg(m_Status);

    return ret;
}

QString CResult::Class() const
{
    return m_Class;
}

void CResult::setClass(const QString &Class)
{
    m_Class = Class;
}

int CResult::Position() const
{
    return m_Position;
}

void CResult::setPosition(int Position)
{
    m_Position = Position;
}

QString CResult::Club() const
{
    return m_Club;
}

void CResult::setClub(const QString &Club)
{
    m_Club = Club;
}

QString CResult::Name() const
{
    return m_Name;
}

void CResult::setName(const QString &Name)
{
    m_Name = Name;
}


void CResult::setStatus(const QString &Status)
{
    m_Status = Status;
}
