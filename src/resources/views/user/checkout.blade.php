<p>決済ページへリダイレクトします。</p> 
<script src="https://js.stripe.com/v3/"></script>
<script>
  // 公開鍵
  const publicKey = '{{ $publicKey }}'
  // 秘密鍵
  const stripe = Stripe(publicKey)

  // このページが読み込まれたら
  window.onload = function() {  
      // stripe決済(チェックアウトページ)に遷移する
    stripe.redirectToCheckout({             
      sessionId: '{{ $session->id }}'         
      }).then(function (result) { // エラーが発生した場合          
        window.location.href = `{{ route('user.cart.cancel') }}`;
        });
  } 
</script>