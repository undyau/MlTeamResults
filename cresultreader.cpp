#include "cresultreader.h"
#include "ciof3xmlcontenthandler.h"
#include <QXmlSimpleReader>
#include <QDebug>

CResultReader::CResultReader(QString a_InFile, QString a_OutFile) : m_InFile(a_InFile)
{
    a_OutFile.replace("\\", "/");
    m_OutFile = a_OutFile;
}

CResultReader::~CResultReader()
{
    Clean();
}

bool resultLessThan( const CResult* e1, const CResult* e2 )
{
    if (e1->Class() != e2->Class())
        return e1->Class() < e2->Class();
    if (e1->Status() != e2->Status())
    {
        if (e1->Status() == "OK")
            return true;
        else if (e2->Status() == "OK")
            return false;
    }
    if (e1->Status() != "OK")
        return e1->Status() < e2->Status();

    return e1->TimeS() < e2-> TimeS();
}

bool clubScoreLessThan( const QPair<QString,int> e1, const QPair<QString, int> e2 )
{
    return e1.second < e2.second;
}

void CResultReader::WriteClassHeader(QString& html, QVector<CResult *> &classResults)
{
    html += QString("<h3>%1 - %2 runners</h3>\r\n").arg(classResults.at(0)->Class()).arg(classResults.size());
}

void CResultReader::WriteResults(QString& html, QVector<CResult *> &classResults)
{
    html += "<br/><small><table>";
    foreach (CResult* res, classResults)
        html += res->ResultHtml();
    html += "</table></small>";
}

void CResultReader::CalcClubScores(QVector<QPair<QString, int>>& scores, QMap<QString, QVector<CResult*>>& clubScorers, int classSize)
{
    foreach(QVector<CResult*> res, clubScorers)
    {
        int score(0);
        QString club = res.at(0)->Club();
        foreach(CResult* r, res)
            score += r->Position();
        if (res.size() < 5)
            score += (classSize + 1) * (5 - res.size());
        QPair<QString,int> pair(club, score);
        scores.append(pair);
    }

    qSort(scores.begin(), scores.end(), clubScoreLessThan);
}

void CResultReader::WriteClubScores(QString& html, QVector<QPair<QString, int>>& scores)
{
    typedef QPair<QString, int> tpair;
    int lastScore(0), pos(0), counter(0);
    html += "<big><table>\r\n";
    foreach(tpair p, scores)
    {
        counter++;
        if (p.second != lastScore)
        {
            pos = counter;
            lastScore = p.second;
        }

        html += QString("<tr><td align='right'>%1.</td><td>%2</td><td align='right'>%3</td>\r\n").arg(pos).arg(p.first).arg(p.second);
    }
    html += "</table></big>\r\n";
}

void CResultReader::WriteClassResult(QString& html, QVector<CResult*>& classResults, QMap<QString, QVector<CResult*>>& clubScorers, int classSize)
{
    if (classResults.size() > 0)
    {
        WriteClassHeader(html, classResults);
        QVector<QPair<QString, int>> scores;
        CalcClubScores(scores, clubScorers, classSize);
        WriteClubScores(html, scores);
        WriteResults(html, classResults);
    }
}

QString CResultReader::toHtml(QString s)
{
    QString t;
    for (int i = 0; i < s.size(); i++)
    {
        if (s.at(i) == "Á") t += "&Aacute;";
        else if (s.at(i) == "á") t += "&aacute;";
        else if (s.at(i) == "À") t += "&Agrave;";
        else if (s.at(i) == "à") t += "&agrave;";
        else if (s.at(i) == "Â") t += "&Acirc;";
        else if (s.at(i) == "â") t += "&acirc;";
        else if (s.at(i) == "Ä") t += "&Auml;";
        else if (s.at(i) == "ä") t += "&auml;";
        else if (s.at(i) == "Ã") t += "&Atilde;";
        else if (s.at(i) == "ã") t += "&atilde;";
        else if (s.at(i) == "Å") t += "&Aring;";
        else if (s.at(i) == "å") t += "&aring;";
        else if (s.at(i) == "Æ") t += "&AElig;";
        else if (s.at(i) == "æ") t += "&aelig;";
        else if (s.at(i) == "Ç") t += "&Ccedil;";
        else if (s.at(i) == "ç") t += "&ccedil;";
        else if (s.at(i) == "Ð") t += "&ETH;";
        else if (s.at(i) == "ð") t += "&eth;";
        else if (s.at(i) == "É") t += "&Eacute;";
        else if (s.at(i) == "é") t += "&eacute;";
        else if (s.at(i) == "È") t += "&Egrave;";
        else if (s.at(i) == "è") t += "&egrave;";
        else if (s.at(i) == "Ê") t += "&Ecirc;";
        else if (s.at(i) == "ê") t += "&ecirc;";
        else if (s.at(i) == "Ë") t += "&Euml;";
        else if (s.at(i) == "ë") t += "&euml;";
        else if (s.at(i) == "Í") t += "&Iacute;";
        else if (s.at(i) == "í") t += "&iacute;";
        else if (s.at(i) == "Ì") t += "&Igrave;";
        else if (s.at(i) == "ì") t += "&igrave;";
        else if (s.at(i) == "Î") t += "&Icirc;";
        else if (s.at(i) == "î") t += "&icirc;";
        else if (s.at(i) == "Ï") t += "&Iuml;";
        else if (s.at(i) == "ï") t += "&iuml;";
        else if (s.at(i) == "Ñ") t += "&Ntilde;";
        else if (s.at(i) == "ñ") t += "&ntilde;";
        else if (s.at(i) == "Ó") t += "&Oacute;";
        else if (s.at(i) == "ó") t += "&oacute;";
        else if (s.at(i) == "Ò") t += "&Ograve;";
        else if (s.at(i) == "ò") t += "&ograve;";
        else if (s.at(i) == "Ô") t += "&Ocirc;";
        else if (s.at(i) == "ô") t += "&ocirc;";
        else if (s.at(i) == "Ö") t += "&Ouml;";
        else if (s.at(i) == "ö") t += "&ouml;";
        else if (s.at(i) == "Õ") t += "&Otilde;";
        else if (s.at(i) == "õ") t += "&otilde;";
        else if (s.at(i) == "Ø") t += "&Oslash;";
        else if (s.at(i) == "ø") t += "&oslash;";
        else if (s.at(i) == "Œ") t += "&OElig;";
        else if (s.at(i) == "œ") t += "&oelig;";
        else if (s.at(i) == "ß") t += "&szlig;";
        else if (s.at(i) == "Þ") t += "&THORN;";
        else if (s.at(i) == "þ") t += "&thorn;";
        else if (s.at(i) == "Ú") t += "&Uacute;";
        else if (s.at(i) == "ú") t += "&uacute;";
        else if (s.at(i) == "Ù") t += "&Ugrave;";
        else if (s.at(i) == "ù") t += "&ugrave;";
        else if (s.at(i) == "Û") t += "&Ucirc;";
        else if (s.at(i) == "û") t += "&ucirc;";
        else if (s.at(i) == "Ü") t += "&Uuml;";
        else if (s.at(i) == "ü") t += "&uuml;";
        else if (s.at(i) == "Ý") t += "&Yacute;";
        else if (s.at(i) == "ý") t += "&yacute;";
        else if (s.at(i) == "Ÿ") t += "&Yuml;";
        else if (s.at(i) == "ÿ") t += "&yuml;";
        else t+= s.at(i);
    }

    return t;
}

void CResultReader::Process()
{
    // Parse the file and create results
    Clean();

    QString html = "<!doctype html><html lang=en><meta charset=utf-8><title>Scores</title>";

    QXmlSimpleReader parser;
    CIof3XmlContentHandler* handler = new CIof3XmlContentHandler(m_Results);
    parser.setContentHandler(handler);
    if (!parser.parse(new QXmlInputSource(new QFile(m_InFile))))
    {
        qDebug() << "Couldn't parse " + m_InFile;
        return;
    }

    // Sort results into Class-Position order
    qSort(m_Results.begin(), m_Results.end(), resultLessThan);

    QMap<QString, QVector<CResult*>> clubScorers;
    QVector<CResult*> classResults;
    QString oClass;
    int classSize(0);

    foreach(CResult* r, m_Results)
    {
        if (r->Class() != oClass)
        {
            // Finish last class
            if (clubScorers.size() > 0)
                WriteClassResult(html, classResults, clubScorers, classSize);

            // Start new class
            clubScorers.clear();
            classResults.clear();
            oClass = r->Class();
            classSize = 0;
        }

        classResults.append(r);
        if (r->Status() == "OK")
        {
            classSize++;
            if (clubScorers.find(r->Club()) == clubScorers.end())
            {
                QVector<CResult*> results;
                clubScorers[r->Club()] = results;
            }
            if (clubScorers[r->Club()].size() < 5)
            {
                clubScorers[r->Club()].append(r);
                r->setScorer(true);
            }
        }

    }

    WriteClassResult(html, classResults, clubScorers, classSize);

    // Write to output file
    QFile output(m_OutFile);
    html = toHtml(html);
    if ( output.open(QIODevice::Truncate|QIODevice::WriteOnly) )
    {
        QTextStream stream( &output );
        stream << html << endl;
    }
}


void CResultReader::Clean()
{
    foreach(CResult* res, m_Results)
        delete res;
}



