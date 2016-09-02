/** 
 * main.cpp
 * $Id$
 */

#include	<QtCore>
#include	<QtGui>
#include	<QtSvg>
#include	"debug.h"

void help()
{
	printf("./svg infile [outfile [suffix]]\n"
		"empty outfile: output stdout\n"
		"empty suffix: suffix png\n");
}

/**
 * argv[1]: infile
 * argv[2]: outfile
 */
int main(int argc, char * argv[])
{
	QApplication app(argc, argv);
	QTextCodec::setCodecForTr(QTextCodec::codecForName("gb18030"));

	bool output = false;
	QString infile;
	QString outfile;
	
	if ( argc > 2 ) {
		outfile = QString(argv[2]);
	}
	if ( argc > 1 ) {
		infile = QString(argv[1]);
	}
	if ( infile.isEmpty() ) {
		printf("empty infile.");
		help();
		return -1;
	}

	if ( outfile.isEmpty() ) {
		outfile = "stdout.png";
		output = true;
	}

	QSvgWidget * svg = new QSvgWidget(0);
	svg->load(infile);
	svg->resize(800, 400);
	/*  */
	QPixmap pixmap = QPixmap();
	pixmap = QPixmap::grabWidget((QWidget*)svg);

	if ( output ) {
		QByteArray bytes;
		QBuffer buf(&bytes);
		buf.open(QIODevice::ReadWrite);
		pixmap.save(&buf, "PNG");
		printf(buf.data().data());
		exit(0);
	} else {
		pixmap.save(outfile);
	}

	return app.exec();
}

