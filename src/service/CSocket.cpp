//
// Created by zhanglei on 19-8-13.
//

#include "include/MainService.h"

using service::CSocket;

CSocket::CSocket()
{
    socket_fd = socket(AF_INET,SOCK_STREAM,0);
}

int CSocket::connect() {

}

int CSocket::accept() {

}

int CSocket::listen(int backLog) {

}

int CSocket::bind() {

}

int CSocket::addOption() {

}

int CSocket::getSocket()
{

}


CSocket::~CSocket() {

}