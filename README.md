#如何安装logSentry

####cmake 安装 NodeSentryClient

    mkdir build

    cd build

    cmake ..

    make -j4


这样我们就可以顺利生成logSentry的二进制文件

然后使用命令

./logSentry -c  配置文件路径  就可以使用了

logSentry 采用异步多线程的模型

配置文件详解:



