$(function(){
  'use strict';

  $('#new_msg').focus();
  $('#fav').load('fav.php');
  $('#users').load('users.php');
  $('#follows').load('follows.php');

  // タブメニュー
  const menuItems = document.querySelectorAll('.menu li a');
  const contents = document.querySelectorAll('.content');

  menuItems.forEach(clickedItem => {
    clickedItem.addEventListener('click', e => {
      e.preventDefault();

      menuItems.forEach(item => {
        item.classList.remove('active');
      });
      clickedItem.classList.add('active');

      contents.forEach(content => {
        content.classList.remove('active');
      });
      document.getElementById(clickedItem.dataset.id).classList.add('active');
    });
  });

  // follow(usersから)
  $('#users').on('click', '.follow', function(){
    // idを取得
    var id = $(this).parents('div').data('id');
  // ajax処理
  $.post('_ajax.php',{
    id: id,
    mode: 'follow'
  }, function(){
    $('#users').load('users.php');
    $('#follows').load('follows.php');
   });
  });

  // deleteFollow(usersから)
  $('#users').on('click', '.followed', function(){
    // idを取得
    var id = $(this).parents('div').data('id');
  // ajax処理
  $.post('_ajax.php',{
    id: id,
    mode: 'deleteFollow'
  }, function(){
    $('#users').load('users.php');
    $('#follows').load('follows.php');
   });
  });

  // follow(followsから)
  $('#follows').on('click', '.follow', function(){
    // idを取得
    var id = $(this).parents('div').data('id');
  // ajax処理
  $.post('_ajax.php',{
    id: id,
    mode: 'follow'
  }, function(){
    $('#follows').load('follows.php');
    $('#users').load('users.php');
   });
  });

  // deleteFollow(followsから)
  $('#follows').on('click', '.followed', function(){
    // idを取得
    var id = $(this).parents('div').data('id');
  // ajax処理
  $.post('_ajax.php',{
    id: id,
    mode: 'deleteFollow'
  }, function(){
    $('#follows').load('follows.php');
    $('#users').load('users.php');
   });
  });

  // favorite(msgから)
  $('#msg').on('click', '.favorite', function(){
    // idを取得
    var id = $(this).parents('div').data('id');
  // ajax処理
  $.post('_ajax.php',{
    id: id,
    mode: 'favorite'
  }, function(res){
    $('#msg_' + id).find('.fav-count').html(res.likes);
    $('#msg_' + id).find('.favorite').removeClass('favorite').addClass('liked');
    $('#fav').load('fav.php');
   });
  });


  // removeFav(msgから)
  $('#msg').on('click', '.liked', function(){
    // idを取得
    var id = $(this).parents('div').data('id');
  // ajax処理
  $.post('_ajax.php',{
    id: id,
    mode: 'deleteFav'
  }, function(res){
    $('#msg_' + id).find('.fav-count').html(res.likes);
    $('#msg_' + id).find('.liked').removeClass('liked').addClass('favorite');
    $('#fav').load('fav.php');
    });

  });

  // removeFav(favから)
  $('#fav').on('click', '.liked', function(){
    // idを取得
    var id = $(this).parents('div').data('id');
  // ajax処理
  $.post('_ajax.php',{
    id: id,
    mode: 'deleteFav'
  }, function(res){
    $('#fav_' + id).find('.fav-count').html(res.likes);
    $('#fav_' + id).find('.liked').removeClass('liked').addClass('favorite');
    $('#fav').load('fav.php');
    $('#msg_' + id).find('.fav-count').html(res.likes);
    $('#msg_' + id).find('.liked').removeClass('liked').addClass('favorite');
    });
  });


  //delete(msgから)
  $('#msg').on('click', '.delete_msg', function(){

    //idを取得
    var id = $(this).parents('div').data('id');
  // ajax処理
  if(confirm('本当に削除しますか?')){
  $.post('_ajax.php',{
    id: id,
    mode: 'delete'
  }, function(){
    $('#msg_' + id).fadeOut(800);
    });
   }
  });

  //delete(favから)
  $('#fav').on('click', '.delete_msg', function(){
    //idを取得
    var id = $(this).parents('div').data('id');
  // ajax処理
  if(confirm('本当に削除しますか?')){
  $.post('_ajax.php',{
    id: id,
    mode: 'delete'
  }, function(){
    $('#fav_' + id).fadeOut(800);
    $('#msg_' + id).remove();
    });
   }
  });
});
