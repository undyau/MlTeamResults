#-------------------------------------------------
#
# Project created by QtCreator 2015-06-20T16:14:16
#
#-------------------------------------------------

QT       += core xml

QT       -= gui

TARGET = MetroXcResults
CONFIG   += console
CONFIG   -= app_bundle

TEMPLATE = app


SOURCES += main.cpp \
    cfilelocator.cpp \
    cresultreader.cpp \
    ciof3xmlcontenthandler.cpp \
    cresult.cpp

HEADERS += \
    cfilelocator.h \
    cresultreader.h \
    ciof3xmlcontenthandler.h \
    cresult.h
