cmake_minimum_required(VERSION 3.7)

project(logSentry)

set(CMAKE_CXX_STANDARD 11)


#定义php源码路径，这里根据自己的真实路径来更改
set(PHP_SOURCE /home/zhanglei/ourc/logSentry/)
# Add -O0 to remove optimizations when using gcc
IF(CMAKE_COMPILER_IS_GNUCC)
    set(CMAKE_CXX_FLAGS_DEBUG "${CMAKE_CXX_FLAGS_DEBUG} -O0 -pthread")
    set(CMAKE_C_FLAGS_DEBUG "${CMAKE_C_FLAGS_DEBUG} -O0 -pthread")
ENDIF(CMAKE_COMPILER_IS_GNUCC)

#引入php需要的扩展源码，这里也是根据自己需要的来更改

set(SOURCE_FILES
        include/ServiceLog.h
        src/service/ServiceLog.cpp
        include/CThread.h
        main.cpp
        include/MainService.h
        include/SingleInstance.h
        src/service/CThread.cpp
        include/Config.h
        src/service/Config.cpp
        include/Lock.h
        src/service/Lock.cpp
        include/CSocket.h
        src/service/CSocket.cpp
        include/IniFileConfig.h
        src/service/IniFileConfig.cpp
        )

add_executable(logSentry ${SOURCE_FILES})
#CFLAGS='-g -O0'
add_custom_target(makefile COMMAND  make clean && phpize && ./configure CFLAGS='-g -O0'  && make && make install WORKING_DIRECTORY ${PROJECT_SOURCE_DIR})
