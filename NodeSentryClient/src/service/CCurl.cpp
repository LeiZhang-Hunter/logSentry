//
// Created by zhanglei on 2019/11/29.
//
#include "MainService.h"
using namespace service;


size_t CCurl::process_data(void *data, size_t size, size_t nmemb, string &content)
{
    long sizes = size * nmemb;
    string temp;
    temp = string((char*)data,sizes);
    content += temp;
    return sizes;
}


int CCurl::httpGet(const char* url,string* returnStr) {
    returnStr->clear();
    CURL* curl = curl_easy_init();
    if(curl)
    {
        CURLcode res;
        curl_easy_setopt(curl, CURLOPT_URL, url);
        curl_easy_setopt(curl, CURLOPT_WRITEDATA, returnStr);
        curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, &process_data);
        res = curl_easy_perform(curl);
        curl_easy_cleanup(curl);
        if(res == CURLE_OK)
        {
            return 0;
        }else{
            return -1;
        }
    }else{
        return  -1;
    }
}