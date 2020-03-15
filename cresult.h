#ifndef CRESULT_H
#define CRESULT_H
#include <QString>

class CResult
{
public:
    CResult();

    QString Status() const;
    void setStatus(const QString &Status);

    QString Name() const;
    void setName(const QString &Name);

    QString Club() const;
    void setClub(const QString &Club);

    int Position() const;
    void setPosition(int Position);

    QString Class() const;
    void setClass(const QString &Class);

    int TimeS() const;
    void setTimeS(int TimeS);

    QString ResultHtml();

    QString Time();
    bool Scorer() const;
    void setScorer(bool Scorer);

private:
    QString m_Status;
    QString m_Name;
    QString m_Club;
    int m_Position;
    QString m_Class;
    int m_TimeS;
    bool m_Scorer;
};

#endif // CRESULT_H
