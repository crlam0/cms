<div class="modal fade" id="basicModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><button class="close" type="button" data-dismiss="modal">x</button>
                <h4 class="modal-title" id="myModalLabel">Оформить заявку</h4>
            </div>
            <div class="modal-body">
                <p>Оставьте Ваши координаты и мы с Вами свяжемся в самое ближайшее время.</p>
                <br />
                <input type="edit" id="request-name" maxlength="32" size="16" class="form-control" placeholder="Ваше имя" required autofocus>
                <br />
                <input type="edit" id="request-phone" maxlength="32" size="16" class="form-control" placeholder="Номер телефона" required>
                <br />
                <div class="form-group">
                  <label for="comment">Коментарий:</label>
                  <textarea class="form-control" rows="5" id="request-comment"></textarea>
                </div>
                <div id="request-result"></div>                        
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" type="button" id="request-button">Оставить заявку</button>
                <button class="btn btn-primary" type="button" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>


        <div id=popupItem>
	<div align=right><span id=popupItemClose align=right>Закрыть</span></div>
	<div id=popupContent></div>
        </div>
        <div id=backgroundPopup></div>


