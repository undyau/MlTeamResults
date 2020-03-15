#ifndef CIOF3XMLCONTENTHANDLER_H
#define CIOF3XMLCONTENTHANDLER_H

#include <QtXml/QXmlDefaultHandler>
#include <QVector>
#include <QSet>
#include "cresult.h"

class CRunner;

class CIof3XmlContentHandler : public QXmlDefaultHandler
{
public:
    CIof3XmlContentHandler(QVector<CResult*>& a_Results);
    ~CIof3XmlContentHandler();
    bool endElement ( const QString & namespaceURI, const QString & localName, const QString & qName );
    bool characters ( const QString & ch );
    bool startElement(const QString & namespaceURI, const QString & localName,
                    const QString & qName, const QXmlAttributes & atts );

private:
    QVector<QString> m_Elements;
    void AddPerson();
    void ProcessClass();
    QString m_FName;
    QString m_SName;
    QString m_Class;
    QString m_Club;
    int m_RaceTime;
    int m_RacePos;
    QString m_RaceStatus;

    QXmlAttributes m_Attributes;
    QVector<CResult*>& m_Results;
};

#endif // CIOF3XMLCONTENTHANDLER_H
