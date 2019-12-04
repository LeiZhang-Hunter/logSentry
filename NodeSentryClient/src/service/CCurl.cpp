//
// Created by zhanglei on 2019/11/29.
//
#include "MainService.h"
using namespace service;

int CCurl::httpGet(const char* url,string returnStr) {
    CURL* curl = curl_easy_init();
    if(curl)
    {
        CURLcode res;
        curl_easy_setopt(curl, CURLOPT_URL, url);
        res = curl_easy_perform(curl);
        curl_easy_setopt(curl,CURLOPT_WRITEDATA,&returnStr);
        curl_easy_cleanup(curl);
    }else{
        return  -1;
    }
}