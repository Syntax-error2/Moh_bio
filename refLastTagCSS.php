<style>
html,
body {
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
    overflow: hidden;
    background: #eef1f5;
}

* {
    box-sizing: border-box;
}

.kiosk-shell {
    width: 100vw;
    height: 100vh;
    padding: 10px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.kiosk-main-grid {
    display: flex;
    gap: 12px;
    width: 100%;
    flex: 1;
    min-height: 0;
}

.kiosk-header-card {
    flex: 0 0 110px;
}

.kiosk-left-col {
    flex: 0 0 66%;
    display: flex;
    flex-direction: column;
    gap: 12px;
    min-height: 0;
}

.kiosk-right-col {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 12px;
    min-height: 0;
}

.kiosk-card {
    background: #f5f5f5;
    border-radius: 14px;
    border: 1px solid #dde2e8;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}

#rcorners4 {
    padding: 12px;
    border-bottom: 2px solid #e0e0e0;
    color: #1a1a1a;
    overflow: hidden;
}

#rcorners4 table,
#rcorners4 td,
#rcorners4 th,
#rcorners4 b,
#rcorners4 strong {
    color: #1a1a1a;
}

.kiosk-activity-card {
    flex: 1;
    padding: 0;
    overflow: hidden;
    min-height: 0;
}

#screenDTimer {
    height: 0;
}

#screen {
    height: 0;
    overflow: hidden;
}

#screen2 {
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.kiosk-stage {
    width: 100%;
    height: 100%;
    padding: 12px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    overflow: hidden;
}

.kiosk-active-main {
    flex: 1;
    min-height: 0;
    display: flex;
    gap: 12px;
    overflow: hidden;
}

.kiosk-active-photo {
    flex: 0 0 33%;
    max-width: 340px;
    min-width: 0;
    border-radius: 12px;
    border: 1px solid #d6dde5;
    background: #f0f3f8;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.kiosk-announcement-card {
    flex: 1;
    min-height: 0;
    padding: 10px;
    overflow: hidden;
}

#screenAnnouncements {
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.kiosk-active-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.kiosk-active-details {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.kiosk-active-lname {
    margin: 0;
    font-size: 56px;
    line-height: 1;
    font-weight: 800;
    color: #202732;
    text-transform: uppercase;
}

.kiosk-active-fname {
    margin: 12px 0 0;
    font-size: 40px;
    line-height: 1.1;
    color: #243640;
}

.kiosk-active-gl {
    margin: 12px 0 0;
    font-size: 26px;
    color: #44576a;
}

.kiosk-active-status {
    flex: 0 0 74px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: #fff;
    padding: 0 18px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
}

.kiosk-active-status-time {
    font-size: 40px;
    font-weight: 700;
}

.kiosk-active-status-flow {
    font-size: 38px;
    font-weight: 700;
    text-transform: uppercase;
}

.kiosk-idle-slider {
    flex: 1;
    min-height: 0;
    border-radius: 12px;
    border: 1px solid #d6dde5;
    background: #f9fbfd;
    box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.02);
    padding: 10px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.kiosk-idle-frame {
    flex: 1;
    min-height: 0;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    background: #dde4ec;
}

.kiosk-idle-frame img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.kiosk-idle-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    border: 0;
    background: transparent;
    padding: 0;
    cursor: pointer;
    color: #f03553;
    font-size: 44px;
    line-height: 1;
    font-weight: 700;
    text-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
}

.kiosk-idle-arrow.left {
    left: 12px;
}

.kiosk-idle-arrow.right {
    right: 12px;
}

.kiosk-idle-dots {
    flex: 0 0 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.kiosk-idle-dot {
    width: 10px;
    height: 10px;
    padding: 0;
    border-radius: 50%;
    border: 2px solid #f03553;
    background: transparent;
    cursor: pointer;
}

.kiosk-idle-dot.active {
    background: #f03553;
}

#rcorners5 {
    flex: 0 0 72px;
    padding: 10px;
    display: flex;
    align-items: center;
}

.barcode-log-group {
    width: 100%;
    height: 50px;
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid #d0d0d0;
    box-shadow: inset 0 0 0 2px rgba(0, 0, 0, 0.04);
}

.barcode-log-icon {
    border: 0;
    min-width: 46px;
    justify-content: center;
    color: #fff;
    font-size: 20px;
    background: linear-gradient(180deg, #ff5d5d 0%, #e03131 100%);
}

.barcode-log-input {
    border: 0;
    height: 50px;
    font-size: 26px;
    font-weight: 500;
    color: #243640;
    padding: 0 14px;
    background: #f6f8fa;
    transition: all 0.2s ease;
}

.barcode-log-input::placeholder {
    color: #7f8b93;
    font-size: 34px;
    font-weight: 400;
}

.barcode-log-input:focus {
    box-shadow: inset 0 0 0 3px rgba(0, 110, 183, 0.15);
    background: #ffffff;
    outline: none;
}

.barcode-log-btn {
    border: 0;
    min-width: 52px;
    color: #fff;
    font-size: 30px;
    background: linear-gradient(180deg, #ff4e67 0%, #e31c3d 100%);
}

.barcode-log-btn:hover,
.barcode-log-btn:focus {
    color: #fff;
    background: linear-gradient(180deg, #ff6077 0%, #ec2949 100%);
    box-shadow: none;
}

#rcorners3 {
    flex: 0 0 180px;
    padding: 18px;
    text-align: center;
    overflow: hidden;
}

#clock {
    font-size: 64px !important;
    line-height: 1;
    margin-bottom: 12px;
    color: #000;
}

#screenRefDate {
    font-size: 48px !important;
    line-height: 1.1;
    color: #000;
}

.kiosk-recent-card {
    flex: 1;
    min-height: 0;
    padding: 10px;
    overflow: hidden;
}

#screen22 {
    width: 100%;
    height: 100%;
    overflow: hidden;
}

div.polaroid {
    width: 40%;
    height: 68%;
    background-color: white;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2), 0 6px 20px rgba(0, 0, 0, 0.19);
    margin: 3% 0% 1% 6%;
    overflow: hidden;
}

div.polaroid2 {
    width: 100%;
    height: 100%;
    background-color: white;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2), 0 6px 20px rgba(0, 0, 0, 0.19);
    margin: 0;
    overflow: hidden;
}

div.polaroid2 img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

@media (max-width: 1366px) {
    .kiosk-shell {
        padding: 8px;
    }

    .kiosk-main-grid {
        gap: 8px;
    }

    .kiosk-left-col,
    .kiosk-right-col {
        gap: 8px;
    }

    .kiosk-header-card {
        flex-basis: 96px;
    }

    #rcorners4 {
        padding: 10px;
    }

    #rcorners5 {
        flex-basis: 62px;
        padding: 8px;
    }

    #rcorners3 {
        flex-basis: 140px;
        padding: 12px;
    }

    #clock {
        font-size: 44px !important;
        margin-bottom: 8px;
    }

    #screenRefDate {
        font-size: 32px !important;
    }

    .barcode-log-input {
        font-size: 20px;
    }

    .barcode-log-input::placeholder {
        font-size: 20px;
    }

    .kiosk-active-lname {
        font-size: 42px;
    }

    .kiosk-active-fname {
        font-size: 30px;
    }

    .kiosk-active-photo {
        flex-basis: 36%;
        max-width: 280px;
    }

    .kiosk-active-gl {
        font-size: 20px;
    }

    .kiosk-active-status {
        flex-basis: 60px;
    }

    .kiosk-active-status-time {
        font-size: 28px;
    }

    .kiosk-active-status-flow {
        font-size: 28px;
    }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
 
</style>
 
    <style>

    #snackbar,
    #snackbar2,
    #snackbar4,
    #snackbar5,
    #snackbar6,
    #snackbar7 {
        z-index: 99999 !important;
    }
     
    #snackbar {
        visibility: hidden;
        min-width: 250px;
        margin-left: -125px;
        background-color: #fe5484;
        color: #fff;
        text-align: center;
        border-radius: 2px;
        padding: 16px;
        position: fixed;
        z-index: 1;
        left: 50%;
        bottom: 30px;
        font-size: 17px;
    }
    
    #snackbar.show {
        visibility: visible;
        -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
        animation: fadein 0.5s, fadeout 0.5s 2.5s;
        background-color: #fe5484;
    }


    #snackbar2 {
        visibility: hidden;
        min-width: 250px;
        margin-left: -125px;
        background-color: #fe5484;
        color: #fff;
        text-align: center;
        border-radius: 2px;
        padding: 16px;
        position: fixed;
        z-index: 1;
        left: 50%;
        bottom: 30px;
        font-size: 17px;
    }
    
    #snackbar2.show2 {
        visibility: visible;
        -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
        animation: fadein 0.5s, fadeout 0.5s 2.5s;
        background-color: #fe5484;
    }
    


    #snackbar4 {
        visibility: hidden;
        min-width: 250px;
        margin-left: -125px;
        background-color: #fe5484;
        color: #fff;
        text-align: center;
        border-radius: 2px;
        padding: 16px;
        position: fixed;
        z-index: 1;
        left: 50%;
        bottom: 30px;
        font-size: 17px;
    }
    
    #snackbar4.show4 {
        visibility: visible;
        -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
        animation: fadein 0.5s, fadeout 0.5s 2.5s;
        background-color: #fe5484;
    }
    
    
    #snackbar5 {
        visibility: hidden;
        min-width: 250px;
        margin-left: -125px;
        background-color: #fe5484;
        color: #fff;
        text-align: center;
        border-radius: 2px;
        padding: 16px;
        position: fixed;
        z-index: 1;
        left: 50%;
        bottom: 30px;
        font-size: 17px;
    }
    
    #snackbar5.show5 {
        visibility: visible;
        -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
        animation: fadein 0.5s, fadeout 0.5s 2.5s;
        background-color: #fe5484;
    }
    
    
    #snackbar7 {
        visibility: hidden;
        min-width: 250px;
        margin-left: -125px;
        background-color: #fe5484;
        color: #fff;
        text-align: center;
        border-radius: 2px;
        padding: 16px;
        position: fixed;
        z-index: 1;
        left: 50%;
        bottom: 30px;
        font-size: 17px;
    }
    
    #snackbar7.show7 {
        visibility: visible;
        -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
        animation: fadein 0.5s, fadeout 0.5s 2.5s;
        background-color: #fe5484;
    }
    
    
    #snackbar6 {
        visibility: hidden;
        min-width: 250px;
        margin-left: -125px;
        background-color: #06a90e;
        color: #fff;
        text-align: center;
        border-radius: 2px;
        padding: 16px;
        position: fixed;
        z-index: 1;
        left: 50%;
        bottom: 30px;
        font-size: 17px;
    }
    
    #snackbar6.show6 {
        visibility: visible;
        -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
        animation: fadein 0.5s, fadeout 0.5s 5.0s;
        background-color: #06a90e;
    }
    
    @-webkit-keyframes fadein {
        from {bottom: 0; opacity: 0;} 
        to {bottom: 30px; opacity: 1;}
    }
    
    @keyframes fadein {
        from {bottom: 0; opacity: 0;}
        to {bottom: 30px; opacity: 1;}
    }
    
    @-webkit-keyframes fadeout {
        from {bottom: 30px; opacity: 1;} 
        to {bottom: 0; opacity: 0;}
    }
    
    @keyframes fadeout {
        from {bottom: 30px; opacity: 1;}
        to {bottom: 0; opacity: 0;}
    }
    

    .kiosk-active-id-badge {
        display: inline-block;
        align-self: flex-start;
        margin: 0 0 8px;
        padding: 4px 10px;
        border-radius: 999px;
        background: #f03553;
        color: #fff;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.4px;
        line-height: 1.2;
    }

    </style>

