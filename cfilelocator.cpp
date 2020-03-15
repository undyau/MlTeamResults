#include "cfilelocator.h"
#include <QDir>
#include <QStringList>
#include <QTextStream>
#include <QDebug>
#include <QRegExp>

CFileLocator::CFileLocator(QString a_Dir, QString a_Prefix) : m_Prefix(a_Prefix)
{
    a_Dir.replace("\\", "/");
    m_Dir = a_Dir;
}

bool CFileLocator::GotFile()
{
    // Search all files matching the directory and prefix
    // Need to get most recent that is an IOF XML 3 result file

    QDir dir(m_Dir);
    QStringList filters;
    filters.push_back(QString(m_Prefix + "*.xml"));
    QStringList list = dir.entryList(filters, QDir::Files, QDir::Time);
    foreach(QString fname, list)
    {
        qDebug() << "Checking" << fname;
        if (IsResultFile(fname, m_EventName, m_EventDate))
        {
            m_LastFoundFile = m_Dir + "/" + fname;
            qDebug() << "Using " << m_LastFoundFile;
            return true;
        }
    }
    return false;
}

QString CFileLocator::FileName()
{
    if (m_LastFoundFile.isEmpty() && !GotFile())
        return "";
    return m_LastFoundFile;
}

void CFileLocator::CleanOldFiles()
{
    if (m_LastFoundFile.isEmpty() || m_EventName.isEmpty() || m_EventDate.isEmpty())
        return;

    QDir dir(m_Dir);
    QStringList filters;
    filters.push_back(QString(m_Prefix + "*.xml"));

    QStringList list = dir.entryList(filters, QDir::Files, QDir::Time | QDir::Reversed);
    foreach(QString fname, list)
    {
        if (fname == m_LastFoundFile)
            continue;

        QString eventName, eventDate;
        if (IsResultFile(fname, eventName, eventDate) &&
            eventName == m_EventName &&
            eventDate == m_EventDate)
            dir.remove(fname);
    }
}

bool CFileLocator::IsResultFile(QString a_FileName, QString& a_EventName, QString& a_EventDate )
{
    QFile file(m_Dir + "/" + a_FileName);
    bool isResultList(false);
    bool isIof3(false);
    QString allText;
    if (file.open(QIODevice::ReadOnly | QIODevice::Text))
    {
        QTextStream in(&file);
        while (!in.atEnd())
        {
            QString line = in.readLine();
            if (line.contains(QString("<ResultList"),Qt::CaseInsensitive))
                isResultList = true;
            if (line.contains(QString("iofVersion=""3.0""")) ||
                line.contains(QString("http://www.orienteering.org/datastandard/3.0")))
                isIof3 = true;
            allText += line;
        }
    }
    else
        qDebug() << "Couldn't open" << a_FileName;
    file.close();

    QRegExp rEvent(".*<Event>(.*)</Event>.*");
    if (rEvent.exactMatch(allText))
    {
        QRegExp rName(".*<Name>(.*)</Name>.*");
        QRegExp rDate(".*<Date>(.*)</Date>.*");
        if (rName.exactMatch(rEvent.cap(1)))
            a_EventName = rName.cap(1);
        if (rDate.exactMatch(rEvent.cap(1)))
            a_EventDate = rDate.cap(1);
    }


    return isIof3 && isResultList;
}
