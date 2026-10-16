<?php
// 客服工作台
include "../includes/common.php";
// 功能权限校验（超级管理员不受限制，仅限制无此权限的员工账号）
adminpermission('workorder', 1);
$title = "客服工作台";
include "./head.php";
if ($islogin != 1) {
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}

// 自动创建聊天相关表结构
if (!$DB->query("SELECT 1 FROM shua_chat_session LIMIT 1")) {
    $DB->exec("CREATE TABLE IF NOT EXISTS shua_chat_session (
        id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        zid INT(11) UNSIGNED DEFAULT '0' COMMENT '账号ID，0表示游客',
        user_ip VARCHAR(50) NOT NULL,
        user_agent TEXT,
        status TINYINT(1) DEFAULT 1,
        create_time DATETIME NOT NULL,
        last_msg_time DATETIME NOT NULL,
        PRIMARY KEY (id),
        INDEX idx_zid (zid),
        INDEX idx_user_ip (user_ip),
        INDEX idx_status (status),
        INDEX idx_last_msg_time (last_msg_time)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} else {
    // 检查并添加 zid 字段（如果旧表没有）
    $columns = $DB->getAll("SHOW COLUMNS FROM shua_chat_session");
    $has_zid = false;
    foreach($columns as $col) {
        if($col['Field'] == 'zid') {
            $has_zid = true;
            break;
        }
    }
    if(!$has_zid) {
        $DB->exec("ALTER TABLE shua_chat_session ADD COLUMN zid INT(11) UNSIGNED DEFAULT '0' COMMENT '账号ID，0表示游客' AFTER id, ADD INDEX idx_zid (zid)");
    }
}

if (!$DB->query("SELECT 1 FROM shua_chat_message LIMIT 1")) {
    $DB->exec("CREATE TABLE IF NOT EXISTS shua_chat_message (
        id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        session_id INT(11) UNSIGNED NOT NULL,
        type TINYINT(1) DEFAULT 0,
        sender VARCHAR(20) NOT NULL,
        content TEXT NOT NULL,
        create_time DATETIME NOT NULL,
        PRIMARY KEY (id),
        INDEX idx_session_id (session_id),
        INDEX idx_create_time (create_time)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

$chat_sound = intval($conf['chat_sound'] ?? 1);
?>

<div class="w-full max-w-[1600px] mx-auto">
    <!-- 音频元素用于客服通知 -->
    <audio id="chat-notification-audio" preload="auto" style="display:none;">
        <source src="/template/default/chat/kefu.wav" type="audio/wav">
    </audio>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <!-- 头部标题栏 -->
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-headphones text-brand-500 mr-2 text-lg"></i> 客服工作台
            </h3>
            <a href="set.php?mod=chat" class="inline-flex items-center justify-center h-9 px-4 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-brand-600 font-medium text-xs transition-colors shadow-sm">
                <i class="fa fa-cog mr-1.5"></i> 客服系统配置
            </a>
        </div>
        
        <!-- 主体内容区 -->
        <div class="p-4 sm:p-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                
                <!-- 左侧：会话列表 (占 4 份) -->
                <div class="lg:col-span-4 flex flex-col" id="chat-session-list">
                    <div class="bg-slate-50 rounded-xl border border-slate-200 overflow-hidden flex flex-col h-[600px]">
                        <div class="px-4 py-3 bg-slate-100 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            当前所有会话
                        </div>
                        <div class="list-group flex-1 overflow-y-auto divide-y divide-slate-100 bg-white" style="margin:0;">
                            <a href="#" class="list-group-item active block p-4 text-sm font-medium">会话列表加载中...</a>
                        </div>
                    </div>
                </div>
                
                <!-- 右侧：消息窗口 (占 8 份) -->
                <div class="lg:col-span-8 flex flex-col" id="chat-message-area">
                    <div class="bg-slate-50 rounded-xl border border-slate-200 overflow-hidden flex flex-col h-[600px]">
                        
                        <!-- 窗口标题 -->
                        <div class="px-5 py-3 bg-white border-b border-slate-200 flex justify-between items-center" id="chat-session-title">
                            <span class="font-bold text-slate-700 text-sm flex items-center"><i class="fa fa-comment-o text-brand-500 mr-2"></i> 请选择左侧会话进行沟通</span>
                            <span class="text-xs text-slate-400">(双击标题可关闭会话)</span>
                        </div>
                        
                        <!-- 消息滚动列表 -->
                        <div class="p-4 sm:p-6 flex-1 overflow-y-auto bg-slate-50/70 space-y-4" id="chat-message-list">
                            <div class="text-center text-slate-400 py-32">
                                <i class="fa fa-inbox text-4xl mb-2 block text-slate-300"></i>
                                暂无会话内容
                            </div>
                        </div>
                        
                        <!-- 底部回复输入框 -->
                        <div class="p-3 bg-white border-t border-slate-200" id="chat-reply-box" style="display:none;">
                            <form id="chat-reply-form" class="flex items-center gap-2" enctype="multipart/form-data">
                                <input type="hidden" name="session_id" value="">
                                
                                <div class="flex-1 relative">
                                    <input type="text" class="w-full h-11 pl-4 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="content" placeholder="输入回复内容...">
                                </div>
                                
                                <input type="file" name="image" accept="image/*" style="display:none;" id="chat-upload-image">
                                <button type="button" class="inline-flex items-center justify-center px-3.5 h-11 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors shrink-0 text-sm font-medium shadow-sm" onclick="$('#chat-upload-image').click();" title="发送图片">
                                    <i class="fa fa-image sm:mr-1"></i><span class="hidden sm:inline">图片</span>
                                </button>
                                
                                <input type="file" name="video" accept="video/mp4" style="display:none;" id="chat-upload-video">
                                <button type="button" class="inline-flex items-center justify-center px-3.5 h-11 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors shrink-0 text-sm font-medium shadow-sm" onclick="$('#chat-upload-video').click();" title="发送视频">
                                    <i class="fa fa-video-camera sm:mr-1"></i><span class="hidden sm:inline">视频</span>
                                </button>
                                
                                <button type="submit" class="inline-flex items-center justify-center px-5 h-11 rounded-xl bg-brand-600 text-white font-medium text-sm hover:bg-brand-700 transition-colors shrink-0 shadow-sm">
                                    <i class="fa fa-send sm:mr-1.5"></i><span class="hidden sm:inline">发送</span>
                                </button>
                            </form>
                        </div>
                        
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<script>
var currentSessionId = null;
var pollingTimer = null;
var lastUnreadCount = 0;
var lastMessageIds = {}; 
var lastMessageListHash = {}; 
var chatSoundEnabled = <?php echo $chat_sound; ?>;

function loadSessionList() {
    $.get('ajax.php?act=chat_session_list', function(res) {
        if(res.code === 0) {
            var html = '';
            var totalUnread = 0;
            if(res.data.length === 0) {
                html = '<div class="p-4 text-center text-slate-400 text-sm">暂无任何会话记录</div>';
            } else {
                res.data.forEach(function(item) {
                    totalUnread += parseInt(item.unread || 0);
                    var displayName = item.username ? item.username : item.user_ip;
                    var isActive = item.id == currentSessionId;
                    
                    html += '<a href="#" class="block p-3.5 transition-colors no-underline '+(isActive ? 'bg-brand-50 border-l-4 border-brand-600' : 'hover:bg-slate-50')+'" data-id="'+item.id+'">'+ 
                        '<div class="flex justify-between items-start mb-1"><span class="font-bold text-slate-800 text-sm truncate max-w-[160px]">'+displayName+'</span>'+(item.unread>0?'<span class="px-2 py-0.5 bg-rose-500 text-white rounded-full text-xs font-bold">'+item.unread+'</span>':'')+'</div>'+
                        '<div class="flex justify-between items-center text-xs text-slate-400"><span class="truncate">'+item.last_msg_time+'</span>'+(item.status==0?'<span class="text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">已关闭</span>':'')+'</div>'+ 
                        '</a>';
                });
            }
            $('#chat-session-list .list-group').html(html);
            
            if(totalUnread > lastUnreadCount) {
                if(chatSoundEnabled) {
                    var audio = document.getElementById('chat-notification-audio');
                    if(audio) {
                        audio.play().catch(function(error) {
                            console.log('音频播放失败:', error);
                        });
                    }
                }
            }
            lastUnreadCount = totalUnread;
        }
    },'json');
}

function loadMessageList(sessionId, scrollBottom, playSound) {
    var currentHeight = $('#chat-message-list')[0].scrollHeight;
    var isAtBottom = $('#chat-message-list').scrollTop() + $('#chat-message-list').height() >= currentHeight - 50;
    
    $.get('ajax.php?act=chat_message_list&session_id='+sessionId, function(res) {
        if(res.code === 0) {
            var currentLastId = lastMessageIds[sessionId] || 0;
            var newUserMessages = [];
            var hasNewUserMessages = false;
            
            var messageHash = '';
            res.data.forEach(function(msg) {
                messageHash += msg.id + '_' + msg.type + '_' + msg.content + '|';
            });
            
            if(lastMessageListHash[sessionId] === messageHash) {
                return;
            }
            
            lastMessageListHash[sessionId] = messageHash;
            
            var html = '';
            res.data.forEach(function(msg) {
                var isAdmin = msg.sender == 'admin';
                if(msg.type==1) {
                    html += '<div class="flex '+(isAdmin ? 'justify-end' : 'justify-start')+'"><div class="max-w-[70%] '+(isAdmin ? 'text-right' : 'text-left')+'"><img src="'+msg.content+'" class="max-w-[200px] max-h-[200px] rounded-xl shadow-sm cursor-pointer border border-slate-200 inline-block" onclick="openImageModal(this.src)"><div class="text-[10px] text-slate-400 mt-1">'+msg.create_time+'</div></div></div>';
                } else if(msg.type==2) {
                    html += '<div class="flex '+(isAdmin ? 'justify-end' : 'justify-start')+'"><div class="max-w-[70%] '+(isAdmin ? 'text-right' : 'text-left')+'"><video src="'+msg.content+'" class="max-w-[240px] max-h-[180px] rounded-xl shadow-sm cursor-pointer border border-slate-200 inline-block" controls onclick="openVideoModal(this.src)"></video><div class="text-[10px] text-slate-400 mt-1">'+msg.create_time+'</div></div></div>';
                } else {
                    html += '<div class="flex '+(isAdmin ? 'justify-end' : 'justify-start')+'"><div class="max-w-[80%] '+(isAdmin ? 'items-end' : 'items-start')+' flex flex-col"><div class="flex items-center space-x-1.5 mb-1"><span class="text-[11px] font-semibold text-slate-500">'+(isAdmin ? '客服' : '用户')+'</span><span class="text-[10px] text-slate-400">'+msg.create_time+'</span></div><div class="px-4 py-2.5 rounded-2xl text-sm leading-relaxed shadow-sm break-all '+(isAdmin ? 'bg-brand-600 text-white rounded-br-none' : 'bg-white text-slate-700 border border-slate-200 rounded-bl-none')+'">'+msg.content+'</div></div></div>';
                }
                
                if(msg.id > currentLastId && msg.sender !== 'admin') {
                    newUserMessages.push(msg);
                    hasNewUserMessages = true;
                }
            });
            
            if(res.data.length > 0) {
                lastMessageIds[sessionId] = res.data[res.data.length - 1].id;
            }
            
            if(hasNewUserMessages && currentLastId > 0 && playSound !== false && chatSoundEnabled) {
                var audio = document.getElementById('chat-notification-audio');
                if(audio) {
                    audio.play().catch(function(error) {
                        console.log('音频播放失败:', error);
                    });
                }
            }
            
            if(html==='') html = '<div class="text-center text-slate-400 py-32"><i class="fa fa-commenting-o text-4xl mb-2 block text-slate-300"></i>暂无消息记录</div>';
            $('#chat-message-list').html(html);
            
            if(scrollBottom!==false || isAtBottom) {
                $('#chat-message-list').scrollTop($('#chat-message-list')[0].scrollHeight);
            }
        }
    },'json');
}

function startPolling() {
    if(pollingTimer) clearInterval(pollingTimer);
    var interval = <?php echo intval($conf['chat_polling']??3); ?> * 1000;
    pollingTimer = setInterval(function(){
        if(currentSessionId) loadMessageList(currentSessionId, true, true);
        loadSessionList();
    }, interval);
}

$(function(){
    loadSessionList();
    startPolling();

    $('#chat-session-list').on('click', 'a', function(e){
        e.preventDefault();
        var sid = $(this).data('id');
        if(!sid) return;
        currentSessionId = sid;
        $('#chat-session-list a').removeClass('bg-brand-50 border-l-4 border-brand-600').addClass('hover:bg-slate-50');
        $(this).removeClass('hover:bg-slate-50').addClass('bg-brand-50 border-l-4 border-brand-600');
        $('#chat-reply-box').show();
        $('#chat-reply-form [name=session_id]').val(sid);
        
        var displayName = $(this).find('.font-bold').text().trim();
        $('#chat-session-title').html('<span class="font-bold text-slate-800 text-sm flex items-center"><i class="fa fa-user-circle text-brand-500 mr-2"></i>当前会话: '+displayName+'</span><span class="text-xs text-slate-400">(双击标题可关闭会话)</span>');
        
        $(this).find('.rounded-full').remove();
        
        loadMessageList(sid, true, false);
        
        setTimeout(function() {
            $.get('ajax.php?act=chat_session_list', function(res) {
                if(res.code === 0) {
                    var totalUnread = 0;
                    res.data.forEach(function(item) {
                        totalUnread += parseInt(item.unread || 0);
                    });
                    lastUnreadCount = totalUnread;
                }
            },'json');
        }, 500);
    });

    $('#chat-reply-form').submit(function(e){
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'ajax.php?act=chat_send_message',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res){
                if(res.code===0){
                    $.get('ajax.php?act=chat_message_list&session_id='+currentSessionId, function(msgRes) {
                        if(msgRes.code === 0 && msgRes.data.length > 0) {
                            lastMessageIds[currentSessionId] = msgRes.data[msgRes.data.length - 1].id + 1;
                        }
                        loadMessageList(currentSessionId, true, false);
                    },'json');
                    $('#chat-reply-form [name=content]').val('');
                    $('#chat-reply-form [name=image]').val('');
                }else{
                    alert(res.msg);
                }
            }
        });
    });

    $('#chat-upload-image').change(function(){
        if(this.files.length>0){
            var formData = new FormData($('#chat-reply-form')[0]);
            $.ajax({
                url: 'ajax.php?act=chat_send_message',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res){
                    if(res.code===0){
                        $.get('ajax.php?act=chat_message_list&session_id='+currentSessionId, function(msgRes) {
                            if(msgRes.code === 0 && msgRes.data.length > 0) {
                                lastMessageIds[currentSessionId] = msgRes.data[msgRes.data.length - 1].id + 1;
                            }
                            loadMessageList(currentSessionId, true, false);
                        },'json');
                        $('#chat-upload-image').val('');
                    }else{
                        alert(res.msg);
                    }
                }
            });
        }
    });

    $('#chat-upload-video').change(function(){
        if(this.files.length>0){
            var formData = new FormData($('#chat-reply-form')[0]);
            $.ajax({
                url: 'ajax.php?act=chat_send_message',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res){
                    if(res.code===0){
                        $.get('ajax.php?act=chat_message_list&session_id='+currentSessionId, function(msgRes) {
                            if(msgRes.code === 0 && msgRes.data.length > 0) {
                                lastMessageIds[currentSessionId] = msgRes.data[msgRes.data.length - 1].id + 1;
                            }
                            loadMessageList(currentSessionId, true, false);
                        },'json');
                        $('#chat-upload-video').val('');
                    }else{
                        alert(res.msg);
                    }
                }
            });
        }
    });

    $('#chat-session-title').on('dblclick', function(){
        if(currentSessionId && confirm('确定要关闭此会话吗？')){
            $.post('ajax.php?act=chat_close_session', {session_id:currentSessionId}, function(res){
                if(res.code===0){
                    loadSessionList();
                    $('#chat-message-list').html('<div class="text-center text-slate-400 py-32">会话已关闭</div>');
                    $('#chat-reply-box').hide();
                }else{
                    alert(res.msg);
                }
            },'json');
        }
    });
});
</script>

<!-- 模态框及动画样式 -->
<style>
#chat-image-modal, #chat-video-modal {
    display: none;
    position: fixed;
    z-index: 999999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(4px);
    overflow: auto;
}
#chat-image-modal-content, #chat-video-modal-content {
    margin: auto;
    display: block;
    max-width: 90%;
    max-height: 90vh;
    border-radius: 12px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
    animation: zoom 0.25s ease;
}
@keyframes zoom {
    from {transform: scale(0.95); opacity: 0;}
    to {transform: scale(1); opacity: 1;}
}
#chat-image-modal-close, #chat-video-modal-close {
    position: absolute;
    top: 20px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.2s;
}
#chat-image-modal-close:hover, #chat-video-modal-close:hover {
    color: #fff;
}
</style>

<!-- 图片放大模态框 -->
<div id="chat-image-modal">
    <span id="chat-image-modal-close">&times;</span>
    <img id="chat-image-modal-content">
</div>

<!-- 视频播放模态框 -->
<div id="chat-video-modal">
    <span id="chat-video-modal-close">&times;</span>
    <video id="chat-video-modal-content" controls></video>
</div>

<script>
function openImageModal(src) {
    var modal = document.getElementById('chat-image-modal');
    var modalImg = document.getElementById('chat-image-modal-content');
    var closeBtn = document.getElementById('chat-image-modal-close');
    
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    modalImg.src = src;
    
    closeBtn.onclick = function() { modal.style.display = 'none'; }
    modal.onclick = function(event) { if (event.target == modal) { modal.style.display = 'none'; } }
}

function openVideoModal(src) {
    var modal = document.getElementById('chat-video-modal');
    var modalVideo = document.getElementById('chat-video-modal-content');
    var closeBtn = document.getElementById('chat-video-modal-close');
    
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    modalVideo.src = src;
    modalVideo.play();
    
    closeBtn.onclick = function() { modal.style.display = 'none'; modalVideo.pause(); }
    modal.onclick = function(event) { if (event.target == modal) { modal.style.display = 'none'; modalVideo.pause(); } }
}
</script>

<?php include "./foot.php";?>
