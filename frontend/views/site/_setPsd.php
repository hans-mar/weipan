    <div class="login-box setpsd">
        <div class="login-wrap">
            <div class="login">
                <div class="exp_tit">
                    <i class="icon-warn"></i><span>商品密码已过期，请重新输入！</span>
                </div>
                <div class="exp_cont">
                   <div>
                        <input type="password" class="exp_text" id="password" name="password" placeholder="请输入商品密码" style="color: #ccc;">
                    </div>
                    <p id="errorMsg" class="exp_p1"></p>
                    <div>
                        <a class="exp_but setPassWord">确定</a>
                    </div>
                    <p class="exp_p2">
                        <a href="<?= url(['site/forget']) ?>">忘记密码?</a>
                    </p>
                </div>
                <div class="box-close">X</div>
            </div>
        </div>

    </div>