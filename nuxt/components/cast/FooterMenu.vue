<template>
    <div class="footer-menu position-fixed bottom-0 start-0 container-fluid p-0 text-center d-lg-none">
        <div class="row m-0 align-items-center ">
            <button class="col p-0">
                <span></span>
                <NuxtLink to="/cast" class="d-flex flex-column justify-content-start link-light link-underline link-underline-opacity-0">
                    <i class="icon-home pt-2 pb-2"></i>
                    <div class="">ホーム</div>
                </NuxtLink>
            </button>
            <button class="col p-0">
                <span></span>
                <NuxtLink to="/cast/order_history?tab=orderAndRequest" class="d-flex flex-column justify-content-start link-light link-underline link-underline-opacity-0">
                    <i class="icon-voice-over-record pt-2 pb-2"></i>
                    <div class="">募集案件</div>
                </NuxtLink>
            </button>
            <button class="col p-0">
                <span></span>
                <NuxtLink to="/cast/order_history?tab=confirmed" class="d-flex flex-column justify-content-start link-light link-underline link-underline-opacity-0">
                <i class="icon-restaurant pt-2 pb-2"></i>
                <div class="">確定案件</div>
                </NuxtLink>
            </button>
            <button class="col p-0">
                <span></span>
                <NuxtLink to="/cast/chat" class="d-flex flex-column justify-content-start link-light link-underline link-underline-opacity-0">
                <i class="icon-chat-bubble pt-2 pb-2"></i>
                <div class="">メッセージ</div>
                </NuxtLink>
            </button>
            <button class="col p-0">
                <span></span>
                <NuxtLink to="/cast/order_history?tab=history" class="d-flex flex-column justify-content-start link-light link-underline link-underline-opacity-0">
                <i class="icon-history pt-2 pb-2"></i>
                <div class="">案件履歴</div>
                </NuxtLink>
            </button>
        </div>
    </div>
</template>


<style scoped>
    @keyframes ripple {
        0% {
            opacity: 1;
            transform: scale(0.0);
        }                
        100% {
            opacity: 0;
            transform: scale(1.0);
        }
    }
    .footer-menu {
        z-index: 99;
        height: var(--footer-menu-height);
        background-color: var(--deet-color-base);
        border-top: 1px solid #414141;
        button {
            background-color: transparent;
            border: none;
            position: relative;
            &:active span::after {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    width: 100px;
                    height: 100px;
                    margin-top: -50px;
                    margin-left: -50px;
                    content: "";
                    background: #493924;
                    border-radius: 100%;
                    opacity: 0;
                    animation: ripple 0.3s 1 ease-out;
                }
 
        }
        >.row {
            height: var(--footer-menu-height);          
            >.col {
                height: var(--footer-menu-height);  
                > a{                    
                    height: var(--footer-menu-height); 
                    > i {
                        font-size: 2.6rem !important;
                    }
                    > div{
                        font-size: 1rem !important;
                    }
                }        
            }
        }
    }
</style>
