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
INCLUDE_DIRECTORIES(BEFORE ./include ./src/service/include ./src/vendor/proto/json/include ./src/vendor/proto/json/src/lib_json)

file(GLOB_RECURSE VENDOR_LIB src/vendor/proto/json/src/lib_json/*.cpp)
add_library(vendor SHARED ${VENDOR_LIB})

file(GLOB_RECURSE SERVICE_LIB src/service/factory/*.cpp src/service/*.cpp)
add_library(service SHARED ${SERVICE_LIB})

file(GLOB_RECURSE APP_LIB src/app/*.cpp)
add_library(app SHARED ${APP_LIB})

set(SOURCE_FILES main.cpp)
add_executable(logSentry ${SOURCE_FILES})
#CFLAGS='-g -O0'
TARGET_LINK_LIBRARIES(logSentry app vendor service)

