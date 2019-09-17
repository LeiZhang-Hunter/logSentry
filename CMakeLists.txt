cmake_minimum_required(VERSION 3.7)
project(logSentry)
set(CMAKE_CXX_STANDARD 11)
#定义php源码路径，这里根据自己的真实路径来更改
# Add -O0 to remove optimizations when using gcc
IF(CMAKE_COMPILER_IS_GNUCC)
    set(CMAKE_CXX_FLAGS_DEBUG "${CMAKE_CXX_FLAGS_DEBUG} -O0 -pthread")
    set(CMAKE_C_FLAGS_DEBUG "${CMAKE_C_FLAGS_DEBUG} -O0 -pthread")
ENDIF(CMAKE_COMPILER_IS_GNUCC)

#引入php需要的扩展源码，这里也是根据自己需要的来更改
INCLUDE_DIRECTORIES(BEFORE ./include ./src/service/include )
set(SOURCE_FILES
        src/service/CServiceLog.cpp
        main.cpp
        src/service/CThread.cpp
        src/app/Config.cpp
        src/service/CLock.cpp
        src/service/CSocket.cpp
        src/service/CIniFileConfig.cpp
        src/service/CProcess.cpp
        src/service/CSignal.cpp
        src/app/MainCenter.cpp
        src/app/FileMonitor.cpp
        src/app/FileMonitorManager.cpp
        src/app/FileMonitor.cpp
        src/service/CEpoll.cpp
        src/service/factory/CProcessFactory.cpp
        src/service/CUnixOs.cpp
        src/service/CThreadSocket.cpp
        src/app/FileMonitorWorker.cpp
        src/service/CPoll.cpp
        src/app/ProtoBuf.cpp
        )
add_executable(logSentry ${SOURCE_FILES})
#CFLAGS='-g -O0'
add_custom_target(makefile COMMAND  make clean && make)
