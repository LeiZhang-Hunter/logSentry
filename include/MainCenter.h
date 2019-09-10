//
// Created by zhanglei on 19-8-16.
//

#ifndef LOGSENTRY_MAINCENTER_H
#define LOGSENTRY_MAINCENTER_H

#endif //LOGSENTRY_MAINCENTER_H

using service::CProcess;

namespace app {

    class MainCenter :public CProcess{
    public:

        static void sigHandle(int sig);
        //配置文件的路径
        bool init(string path);
        void start();
        bool destroy();
    };
}