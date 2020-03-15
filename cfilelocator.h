#ifndef CFILELOCATOR_H
#define CFILELOCATOR_H
#include <QString>


class CFileLocator
{
public:
    CFileLocator(QString a_Dir, QString a_Prefix);
    bool GotFile();
    QString FileName();
    void CleanOldFiles();

private:
    QString m_Dir;
    QString m_Prefix;
    QString m_LastFoundFile;
    bool IsResultFile(QString a_FileName, QString& a_EventName, QString& a_EventDate );
    QString m_EventName;
    QString m_EventDate;
};

#endif // CFILELOCATOR_H
