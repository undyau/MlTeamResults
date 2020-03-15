#ifndef CRESULTREADERR_H
#define CRESULTREADERR_H
#include <QString>
#include "cresult.h"
#include <QVector>
#include <QMap>

class CResultReader
{
public:
    CResultReader(QString a_InFile, QString a_OutFile);
    ~CResultReader();
    void Process();

    void WriteClassResult(QString& html, QVector<CResult*>& classResults, QMap<QString, QVector<CResult*>>& clubScorers, int classSize);
    void WriteClassHeader(QString &html, QVector<CResult *> &classResults);
    void CalcClubScores(QVector<QPair<QString, int> > &scores, QMap<QString, QVector<CResult *> > &clubScorers, int classSize);
    void WriteClubScores(QString &html, QVector<QPair<QString, int> > &scores);
    QString toHtml(QString s);
    void WriteResults(QString &html, QVector<CResult *> &classResults);
private:
    QString m_InFile;
    QString m_OutFile;
    QVector <CResult*> m_Results;

    void Clean();
};

#endif // CRESULTREADERR_H
