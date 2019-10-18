cmake_minimum_required(VERSION 3.7)
project(logSentry)
set(CMAKE_CXX_STANDARD 11)
#定义php源码路径，这里根据自己的真实路径来更改
# Add -O0 to remove optimizations when using gcc
IF(CMAKE_COMPILER_IS_GNUCC)
    set(CMAKE_CXX_FLAGS_DEBUG "${CMAKE_CXX_FLAGS_DEBUG} -O0 -pthread")
    set(CMAKE_C_FLAGS_DEBUG "${CMAKE_C_FLAGS_DEBUG} -O0 -pthread")
ENDIF(CMAKE_COMPILER_IS_GNUCC)

#引入头文件
INCLUDE_DIRECTORIES(BEFORE ./include ./src/service/include ./src/vendor/proto/json/include ./src/vendor/proto/json/src/lib_json)

file(GLOB_RECURSE VENDOR_LIB src/vendor/proto/json/src/lib_json/*.cpp)
#生成vendor库
add_library(vendor SHARED ${VENDOR_LIB})

file(GLOB_RECURSE SERVICE_LIB src/service/factory/*.cpp src/service/*.cpp)
#生成service库
add_library(service SHARED ${SERVICE_LIB})

file(GLOB_RECURSE APP_LIB src/app/*.cpp)
#生成app库
add_library(app SHARED ${APP_LIB})

set(SOURCE_FILES main.cpp)
#加入主要文件
add_executable(logSentry ${SOURCE_FILES})
#CFLAGS='-g -O0'
#进行动态库连接
TARGET_LINK_LIBRARIES(logSentry app vendor service)

