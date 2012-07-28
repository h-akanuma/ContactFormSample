<?php

/**
 * Contact Form Functional Tests
 * 
 * @group Functional
 */
class Test_Functional_Form extends FunctionalTestCase {

	public function test_入力ページにアクセス() {
		try  {
			static::$crawler = static::$client->request('GET', static::open('form'));
		} catch (Exception $e) {
			echo $e->getMessage(), PHP_EOL, 'Error: レスポンスエラーです。', PHP_EOL;
			exit;
		}
		
		//var_dump(static::$client->getResponse()->getContent());
		//exit;
		
		$this->assertNotNull(static::$crawler);
	}
	
	public function test_レスポンスコードの確認() {
		$this->assertEquals(200, static::$client->getResponse()->getStatus());
	}
	
	public function test_レスポンスヘッダの確認() {
		$test = static::$client->getResponse()->getHeader('Content-Type');
		$expected = 'text/html; charset=UTF-8';
		$this->assertEquals($expected, $test);
	}
	
	public function test_titleとh1の確認() {
		$test = 'コンタクトフォーム';
		$this->assertEquals($test, static::$crawler->filter('title')->text());
		
		$this->assertEquals('お問い合わせ', static::$crawler->filter('h1')->text());
	}
	
	public function test_空欄のまま確認ボタンを押す() {
		$form = static::$crawler->selectButton('form_submit')->form();
		static::$crawler = static::$client->submit($form);
		
		$test = 'コンタクトフォーム：エラー';
		$this->assertEquals($test, static::$crawler->filter('title')->text());
		
		$test = static::$crawler->filter('li')->text();
		$expected = '名前 欄は必須です。';
		$this->assertEquals($expected, $test);
		
		$test = static::$crawler->filter('li')->eq(1)->text();
		$expected = 'メールアドレス 欄は必須です。';
		$this->assertEquals($expected, $test);
		
		$test = static::$crawler->filter('li')->eq(2)->text();
		$expected = 'コメント 欄は必須です。';
		$this->assertEquals($expected, $test);
	}
	
	public function test_名前にタブを含める(){
		$form = static::$crawler->selectButton('form_submit')->form();
		static::$crawler = static::$client->submit($form, array(
			'name' => "abc\txyz",
			'email' => '',
			'comment' => '',
		));
		
		$test = 'コンタクトフォーム：エラー';
		$this->assertEquals($test, static::$crawler->filter('title')->text());
		
		$test = static::$crawler->filter('li')->text();
		$expected = '名前 欄にはタブや改行を含めないようにしてください。';
		$this->assertEquals($expected, $test);
	} 
	
	public function test_メールアドレスに改行を含める() {
		$form = static::$crawler->selectButton('form_submit')->form();
		static::$crawler = static::$client->submit($form, array(
			'name' => '',
			'email' => "foo@example.jp\nbar",
			'comment' => '',
		));
		
		$test = 'コンタクトフォーム：エラー';
		$this->assertEquals($test, static::$crawler->filter('title')->text());
		
		$test = static::$crawler->filter('li')->eq(1)->text();
		$expected = 'メールアドレス 欄にはタブや改行を含めないようにしてください。';
		$this->assertEquals($expected, $test);
	}
	
	public function test_最大文字数を超えて入力() {
		$form = static::$crawler->selectButton('form_submit')->form();
		static::$crawler = static::$client->submit($form, array(
			'name' => str_repeat('あ', 51),
			'email' => str_repeat('a', 90) . '@example.jp',
			'comment' => str_repeat('あ', 401),
		));
		
		$test = 'コンタクトフォーム：エラー';
		$this->assertEquals($test, static::$crawler->filter('title')->text());
		
		$test = static::$crawler->filter('li')->text();
		$expected = '名前 欄は 50 文字を超えないようにしてください。';
		$this->assertEquals($expected, $test);
		
		$test = static::$crawler->filter('li')->eq(1)->text();
		$expected = 'メールアドレス 欄は 100 文字を超えないようにしてください。';
		$this->assertEquals($expected, $test);
		
		$test = static::$crawler->filter('li')->eq(2)->text();
		$expected = 'コメント 欄は 400 文字を超えないようにしてください。';
		$this->assertEquals($expected, $test);
	}
	
}