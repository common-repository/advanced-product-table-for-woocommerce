function iwptp_sfsi_clean_links(){
  jQuery('[href]', '.sfsi_shortcode_container').each(function(){
    var $this = jQuery(this),
        href = $this.attr('href'),
        over_ajax = false;

    if( href.indexOf('%3Fwc-ajax%3Diwptp_ajax') !== -1 ){
        var i = href.indexOf('%3Fwc-ajax%3Diwptp_ajax'),
            href = href.substring( 0, i );
        $this.attr('href', href);
        over_ajax = true;
    }

    if( href.indexOf('?wc-ajax=iwptp_ajax') !== -1 ){
        var i = href.indexOf('?wc-ajax=iwptp_ajax'),
            href = href.substring( 0, i );
        $this.attr('href', href);
        over_ajax = true;
    }

    if( over_ajax ){
        href = href.replace(/%25/g, "%");
    }

    if( href.indexOf('_device') !== -1 ){
      var $container = jQuery('.iwptp'),
          query_string = $container.attr('data-iwptp-query-string');

      var i = href.indexOf(query_string);
      if( i !== -1 ){
        href = href.substring( 0, i );
      }

      $this.attr('href', href);
    }

    if( href.indexOf('%3F') !== -1 ){
        var i = href.indexOf('%3F'),
            href = href.substring( 0, i );
        $this.attr('href', href);
    }
  })
}

jQuery(function(){
  iwptp_sfsi_clean_links();
})

function iwptp_sfsi_init(){ 

  iwptp_sfsi_clean_links();

//changes done {Monad}
//putting it before to make sure it registers before the mobile click function
  SFSI(document).on('click','.inerCnt a[href=""]',function(event){
      //check if not mobile
      if(!(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))) {
          //execute
          // console.log('abc');
          event.preventDefault();
      }
  });


  SFSI("head").append('<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />'), 
  SFSI("head").append('<meta http-equiv="Pragma" content="no-cache" />'), SFSI("head").append('<meta http-equiv="Expires" content="0" />'), 
  SFSI(document).click(function(s) {
      var i = SFSI(".sfsi_FrntInner"), e = SFSI(".sfsi_wDiv"), t = SFSI("#at15s");
  i.is(s.target) || 0 !== i.has(s.target).length || e.is(s.target) || 0 !== e.has(s.target).length || t.is(s.target) || 0 !== t.has(s.target).length || i.fadeOut();
  }), SFSI("div#sfsiid_linkedin").find(".icon4").find("a").find("img").mouseover(function() {
      SFSI(this).attr("src", sfsi_icon_ajax_object.plugin_url + "images/visit_icons/linkedIn_hover.svg");
  }), SFSI("div#sfsiid_linkedin").find(".icon4").find("a").find("img").mouseleave(function() {
      SFSI(this).attr("src", sfsi_icon_ajax_object.plugin_url + "images/visit_icons/linkedIn.svg");
  }), SFSI("div#sfsiid_youtube").find(".icon1").find("a").find("img").mouseover(function() {
      SFSI(this).attr("src", sfsi_icon_ajax_object.plugin_url + "images/visit_icons/youtube_hover.svg");
  }), SFSI("div#sfsiid_youtube").find(".icon1").find("a").find("img").mouseleave(function() {
      SFSI(this).attr("src", sfsi_icon_ajax_object.plugin_url + "images/visit_icons/youtube.svg");
  }), SFSI("div#sfsiid_facebook").find(".icon1").find("a").find("img").mouseover(function() {
      SFSI(this).css("opacity", "0.9");
  }), SFSI("div#sfsiid_facebook").find(".icon1").find("a").find("img").mouseleave(function() {
      SFSI(this).css("opacity", "1");
  /*{Monad}*/
  }), SFSI("div#sfsiid_twitter").find(".cstmicon1").find("a").find("img").mouseover(function() {
      SFSI(this).css("opacity", "0.9");
  }), SFSI("div#sfsiid_twitter").find(".cstmicon1").find("a").find("img").mouseleave(function() {
      SFSI(this).css("opacity", "1");
  }), SFSI(".pop-up").on("click", function() {
      ("fbex-s2" == SFSI(this).attr("data-id")  || "linkex-s2" == SFSI(this).attr("data-id")) && (SFSI("." + SFSI(this).attr("data-id")).hide(), 
      SFSI("." + SFSI(this).attr("data-id")).css("opacity", "1"), SFSI("." + SFSI(this).attr("data-id")).css("z-index", "1000")), 
      SFSI("." + SFSI(this).attr("data-id")).show("slow");
  }), /*SFSI("#close_popup").live("click", function() {*/SFSI(document).on("click", '#close_popup', function () {
      SFSI(".read-overlay").hide("slow");
  });
  var e = 0; 
  sfsi_make_popBox(), SFSI('input[name="sfsi_popup_text"] ,input[name="sfsi_popup_background_color"],input[name="sfsi_popup_border_color"],input[name="sfsi_popup_border_thickness"],input[name="sfsi_popup_fontSize"],input[name="sfsi_popup_fontColor"]').on("keyup", sfsi_make_popBox), 
  SFSI('input[name="sfsi_popup_text"] ,input[name="sfsi_popup_background_color"],input[name="sfsi_popup_border_color"],input[name="sfsi_popup_border_thickness"],input[name="sfsi_popup_fontSize"],input[name="sfsi_popup_fontColor"]').on("focus", sfsi_make_popBox), 
  SFSI("#sfsi_popup_font ,#sfsi_popup_fontStyle").on("change", sfsi_make_popBox), 
  /*SFSI(".radio").live("click", function() {*/
SFSI(document).on("click", '.radio', function () {
      var s = SFSI(this).parent().find("input:radio:first");
      "sfsi_popup_border_shadow" == s.attr("name") && sfsi_make_popBox();
  }), /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ? SFSI("img.sfsi_wicon").on("click", function(s) {
      if(SFSI(s.target).parent().attr('href')=="" ){
          s.preventDefault();
      }
      if(!SFSI(this).hasClass('sfsi_click_wicon')){
          s.stopPropagation&&s.stopPropagation();
      }
      var i = SFSI("#sfsi_floater_sec").val();
      SFSI("div.sfsi_wicons").css("z-index", "0"), SFSI(this).parent().parent().parent().siblings("div.sfsi_wicons").find(".inerCnt").find("div.sfsi_tool_tip_2").hide(), 
      SFSI(this).parent().parent().parent().parent().siblings("li").length > 0 && (SFSI(this).parent().parent().parent().parent().siblings("li").find("div.sfsi_tool_tip_2").css("z-index", "0"), 
      SFSI(this).parent().parent().parent().parent().siblings("li").find("div.sfsi_wicons").find(".inerCnt").find("div.sfsi_tool_tip_2").hide()), 
      SFSI(this).parent().parent().parent().css("z-index", "1000000"), SFSI(this).parent().parent().css({
          "z-index":"999"
      }), SFSI(this).attr("data-effect") && "fade_in" == SFSI(this).attr("data-effect") && (SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").css({
          opacity:1,
          "z-index":10
      }), SFSI(this).parent().css("opacity", "1")), SFSI(this).attr("data-effect") && "scale" == SFSI(this).attr("data-effect") && (SFSI(this).parent().addClass("scale"), 
      SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").css({
          opacity:1,
          "z-index":10
      }), SFSI(this).parent().css("opacity", "1")), SFSI(this).attr("data-effect") && "combo" == SFSI(this).attr("data-effect") && (SFSI(this).parent().addClass("scale"), 
      SFSI(this).parent().css("opacity", "1"), SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").css({
          opacity:1,
          "z-index":10
      })), ("top-left" == i || "top-right" == i) && SFSI(this).parent().parent().parent().parent("#sfsi_floater").length > 0 && "sfsi_floater" == SFSI(this).parent().parent().parent().parent().attr("id") ? (SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").addClass("sfsi_plc_btm"), 
      SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").find("span.bot_arow").addClass("top_big_arow"), 
      SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").css({
          opacity:1,
          "z-index":10
      }), SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").show()) :(SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").find("span.bot_arow").removeClass("top_big_arow"), 
      SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").removeClass("sfsi_plc_btm"), 
      SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").css({
          opacity:1,
          "z-index":1e3
      }), SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").show());
  }) :SFSI("img.sfsi_wicon").on("mouseenter", function() {
      var s = SFSI("#sfsi_floater_sec").val();
      SFSI("div.sfsi_wicons").css("z-index", "0"), SFSI(this).parent().parent().parent().siblings("div.sfsi_wicons").find(".inerCnt").find("div.sfsi_tool_tip_2").hide(), 
      SFSI(this).parent().parent().parent().parent().siblings("li").length > 0 && (SFSI(this).parent().parent().parent().parent().siblings("li").find("div.sfsi_tool_tip_2").css("z-index", "0"), 
      SFSI(this).parent().parent().parent().parent().siblings("li").find("div.sfsi_wicons").find(".inerCnt").find("div.sfsi_tool_tip_2").hide()), 
      SFSI(this).parent().parent().parent().css("z-index", "1000000"), SFSI(this).parent().parent().css({
          "z-index":"999"
      }), SFSI(this).attr("data-effect") && "fade_in" == SFSI(this).attr("data-effect") && (SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").css({
          opacity:1,
          "z-index":10
      }), SFSI(this).parent().css("opacity", "1")), SFSI(this).attr("data-effect") && "scale" == SFSI(this).attr("data-effect") && (SFSI(this).parent().addClass("scale"), 
      SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").css({
          opacity:1,
          "z-index":10
      }), SFSI(this).parent().css("opacity", "1")), SFSI(this).attr("data-effect") && "combo" == SFSI(this).attr("data-effect") && (SFSI(this).parent().addClass("scale"), 
      SFSI(this).parent().css("opacity", "1"), SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").css({
          opacity:1,
          "z-index":10
      })), ("top-left" == s || "top-right" == s) && SFSI(this).parent().parent().parent().parent("#sfsi_floater").length > 0 && "sfsi_floater" == SFSI(this).parent().parent().parent().parent().attr("id") ? (SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").addClass("sfsi_plc_btm"), 
      SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").find("span.bot_arow").addClass("top_big_arow"), 
      SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").css({
          opacity:1,
          "z-index":10
      }), SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").show()) :(SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").find("span.bot_arow").removeClass("top_big_arow"), 
      SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").removeClass("sfsi_plc_btm"), 
      SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").css({
          opacity:1,
          "z-index":10
      }), SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").show());
  }), SFSI("div.sfsi_wicons").on("mouseleave", function() {
      SFSI(this).children("div.inerCnt").children("a.sficn").attr("data-effect") && "fade_in" == SFSI(this).children("div.inerCnt").children("a.sficn").attr("data-effect") && SFSI(this).children("div.inerCnt").find("a.sficn").css("opacity", "0.6"), 
      SFSI(this).children("div.inerCnt").children("a.sficn").attr("data-effect") && "scale" == SFSI(this).children("div.inerCnt").children("a.sficn").attr("data-effect") && SFSI(this).children("div.inerCnt").find("a.sficn").removeClass("scale"), 
      SFSI(this).children("div.inerCnt").children("a.sficn").attr("data-ffect") && "combo" == SFSI(this).children("div.inerCnt").children("a.sficn").attr("data-effect")/*  && SFSI(this).children("div.inerCnt").find("a.sficn").css("opacity", "0.6"), */
  }), SFSI("body").on("click", function(){
      SFSI(".inerCnt").find("div.sfsi_tool_tip_2").hide();
  }), SFSI(".adminTooltip >a").on("hover", function() {
      SFSI(this).offset().top, SFSI(this).parent("div").find("div.sfsi_tool_tip_2_inr").css("opacity", "1"), 
      SFSI(this).parent("div").find("div.sfsi_tool_tip_2_inr").show();
  }), SFSI(".adminTooltip").on("mouseleave", function() {
      "none" != SFSI(".gpls_tool_bdr").css("display") && 0 != SFSI(".gpls_tool_bdr").css("opacity") ? SFSI(".pop_up_box ").on("click", function() {
          SFSI(this).parent("div").find("div.sfsi_tool_tip_2_inr").css("opacity", "0"), SFSI(this).parent("div").find("div.sfsi_tool_tip_2_inr").hide();
      }) :(SFSI(this).parent("div").find("div.sfsi_tool_tip_2_inr").css("opacity", "0"), 
      SFSI(this).parent("div").find("div.sfsi_tool_tip_2_inr").hide());
  }), SFSI(".expand-area").on("click", function() {
      "Read more" == SFSI(this).text() ? (SFSI(this).siblings("p").children("label").fadeIn("slow"), 
      SFSI(this).text("Collapse")) :(SFSI(this).siblings("p").children("label").fadeOut("slow"), 
      SFSI(this).text("Read more"));
  }), SFSI(".sfsi_wDiv").length > 0 && setTimeout(function() {
      var s = parseInt(SFSI(".sfsi_wDiv").height()) + 15 + "px";
      SFSI(".sfsi_holders").each(function() {
          SFSI(this).css("height", s);
    SFSI(".sfsi_widget");
      });
  }, 200);
};
