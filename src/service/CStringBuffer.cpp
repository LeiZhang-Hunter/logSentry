//
// Created by root on 19-10-2.
//
#include "MainService.h"

using namespace service;

CStringBuffer::CStringBuffer(const char *string) {
    ourString=string;
}


std::string CStringBuffer::getBuffer()
{
    std::cout<<"ourString2:"<<ourString<<std::endl;
    return ourString;
}