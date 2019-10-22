//
// Created by zhanglei on 19-8-23.
//

#ifndef LOGSENTRY_CUNIXOS_H
#define LOGSENTRY_CUNIXOS_H

#endif //LOGSENTRY_CUNIXOS_H

namespace service{
    class CUnixOs
    {
    public:
        bool getRlimit(int resource,struct rlimit *rlim);

        bool is_file(const char* dir);

        bool is_dir(const char* dir);

        uint64_t htonll(uint64_t number);

        uint64_t ntohll(uint64_t number);
    };
}

