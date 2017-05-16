<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-heading">判题机配置教程</div>
			<div class="panel-body">
				<p>由于判题机可能是基于分布式的，不方便使用PHP直接进行管理，所以特在此写下判题机配置教程，</p>
				<p>准备工作，检查以下选项</p>
				<ul>
					<li>请检测判题机操作系统版本，只能使用CentOS 7</li>
					<li>检查判题核心是否支持以下编译器，GCC 4.8.5以上 检查方式运行<code>GCC --version</code>，如果没有请安装<code>yum
							install gcc</code></li>
					<li>还需要支持G++，4.8.5以上，Java1.6以上即可，不过测试的时候使用的是1.8</li>
					<li>安装Java步骤如下</li>
					<li>下载Java JDK 1.8,不要使用wget 无法下载，请在其他地方下载以后上传至/root</li>
					<li>安装rpm包 <code>rpm -ivh jdk-8u121-linux-x64.rpm</code></li>
					<li>输入<code>javac</code>命令测试是否安装成功
					</li>
					<li>若有其他语言，请自行添加，并重新编译判题核心。</li>
				</ul>
				<p>判题核心需要以下内容</p>
				<ul>
					<li>安装mysqlclient <code>yum install mysql-devel</code></li>
					<li>安装静态编译库 <code>yum install libstdc++-static</code></li>
					<li><code>yum install glibc-static</code></li>
					<li>编译判题核心 <code>gcc -o judge_client client.cpp -L/usr/lib64/mysql
							-lmysqlclient</code></li>
				</ul>
				<p>判题核心守护进程需要以下内容</p>
				<ul>
					<li>安装hiredis</li>
					<li>下载zip源码包 <code>wget
							https://github.com/redis/hiredis/archive/master.zip</code></li>
					<li>解压 <code>unzip master.zip</code></li>
					<li>进入解压的文件中 <code>cd hiredis-master</code></li>
					<li>安装源码包 <code>make & make install</code></li>
					<li>修改动态链接库地址 <code>vim /etc/ld.so.conf</code> 新增内容<code>/usr/local/lib</code></li>
					<li>重新载入<code>ldconfig</code></li>
					<li>编译守护进程 <code>g++ -o judged server.c -std=c++11 -lhiredis</code></li>
				</ul>
				<p>配置判题机工作环境</p>
				<ul>
					<li>新建judge用户<code>useradd --uid 2017 judge</code></li>

					<li>切换到judge工作目录 <code>cd /home/judge</code></li>
					<li>新建如下文件夹 <code>mkdir data etc log run0 run1 run2 run3 run4 run5
							run6 run7 run8 run9</code>
						run0-9是工作空间，一般允许多少个判题进程同时工作就把这个值设为其最大值，一般允许10个判题进程工作
					</li>
					<li>更改工作空间权限 <code>chown judge run*</code></li>
					<li>进入etc目录，复制配置文件 <code>cd etc</code>,如果没有请使用vim重新编辑judge.conf一个内容如下(去掉#后面的内容)
					</li>
					<pre>
OJ_HOST_NAME=127.0.0.1 #OJ的数据库服务器的地址
OJ_USER_NAME=root      #OJ的数据库用户名	   
OJ_PASSWORD=129283t    #OJ的数据库密码
OJ_DB_NAME=oj          #OJ的数据库表名称
OJ_PORT_NUMBER=3306	   #OJ的数据库端口号
OJ_RUNID=x  		   #当前判题机的编号，不同服务器上保持唯一性，插入数据库status·judger字段
OJ_JAVA_TIME_BONUS=2   #Java相关配置
OJ_JAVA_MEMORY_BONUS=512
OJ_JAVA_XMS=-Xms32m
OJ_JAVA_XMX=-Xmx256m
OJ_SIM_ENABLE=0		   #是否开启代码查重
OJ_OI_MODE=0      	   #是否启用OI判题模式
OJ_SHM_RUN=0           #是否使用shm
OJ_USE_MAX_TIME=1      #是否使用所有数据中运行时间最长的数据作为最后的运行时间
#以下配置仅用于judged 守护进程
OJ_RUNNING=10          #最大允许几个判题进程工作，小于或等于工作目录个数（run*）
OJ_SLEEP_TIME=5        #OJ无法连接redis时的重连时间
OJ_LANG_SET=1,2,3      #判题语言选项和@Config/config.php的langArr对应，不能使用0
OJ_REDISSERVER=127.0.0.1 #Redis的服务器地址
OJ_REDISPORT=6379      #Redis端口号
OJ_REDISAUTH=FALSE     #Redis授权，没有必须是FALSE
					</pre>
					<li>一般情况下，新的判题机配置文件无须改动任何内容</li>
					<li>数据库服务器一般是开启数据库和redis端口的，不需要调整。</li>
				</ul>
				<p>同步判题数据</p>
				<ul>
					<li>使用scp命令<code>scp -r root@xxx.xxx.xxx.xxx:/home/judge/data
							/home/judge/</code></li>
					<li>xxx.xxx.xxx.xxx为远程服务器IP地址</li>
					<li>第一次同步时，速度很慢,如果是远程服务器更新了判题文件，需要同步，请使用<code>scp -r
							/home/judge/data/yyyy root@xxx.xxx.xx.xxx.xx:/home/judge/data/</code></li>
					<li>yyyy表示题目编号</li>
				</ul>
				<p>启动判题机</p>
				<ul>
					<li>移动编译好的核心到/usr/bin/目录下</li>
					<li><code>mv judged /usr/bin</code></li>
					<li><code>mv judge_client /usr/bin</code></li>
					<li>设置守护进程开机启动</li>
					<li>切换到/etc/init.d目录下<code>cd /etc/init.d</code></li>
					<li>新建startup.sh脚本 <code>vim startup.sh</code>内容如下</li>
				<pre>
#!/bin/sh
#This script write by gscsdlz on 2017-3-27
#To init the system when it start
#about httpd, mysqld, network, redisd...etc

#start judged
/usr/bin/judged
</pre>
				<li>切换文件到可执行权限 <code>chmod 755 startup.sh</code></li>
				<li>启动守护进程的DEBUG模式，检查配置是否成功</li>
				<li><code>/usr/bin/judged /home/judge DEBUG</code></li>
				</ul>
			</div>
		</div>
	</div>
</div>
