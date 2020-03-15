#include <QCoreApplication>
#include <QDebug>
#include <QStandardPaths>
#include "cfilelocator.h"
#include "cresultreader.h"
int main(int argc, char *argv[])
{
    QCoreApplication a(argc, argv);
    QString inDir, prefix, outFile;
    inDir = QStandardPaths::standardLocations(QStandardPaths::DesktopLocation).at(0);
    outFile = QStandardPaths::standardLocations(QStandardPaths::DesktopLocation).at(0)  + QString("/output.html");

    if (a.arguments().size() > 1)
        inDir = a.arguments().at(1);
    if (a.arguments().size() > 2)
        prefix = a.arguments().at(2);
    if (a.arguments().size() > 3)
        outFile = a.arguments().at(3);

    CFileLocator f(inDir, prefix);
    if (f.GotFile())
    {
        qDebug() << "Found file" << f.FileName();
        CResultReader r(f.FileName(), outFile);
        r.Process();
    }
    f.CleanOldFiles();

    return 0;
}
