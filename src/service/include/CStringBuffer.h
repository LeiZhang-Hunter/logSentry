//
// Created by root on 19-10-2.
//

#ifndef LOGSENTRY_CSTRINGBUFFER_H
#define LOGSENTRY_CSTRINGBUFFER_H

#endif //LOGSENTRY_CSTRINGBUFFER_H

namespace service {
    class CStringBuffer {
    public:
        CStringBuffer(const char* string);

        CStringBuffer& operator<<(const char* string)
        {
            ourString = ourString.append(string);
            return *this;
        }
        CStringBuffer& operator<<(int data)
        {
            ourString = ourString.append(std::to_string(data));
            return *this;
        }
        CStringBuffer& operator<<(long data)
        {
            ourString = ourString.append(std::to_string(data));
            return *this;
        }
        std::string ourString;
        std::string getBuffer();

    private:
    };
}