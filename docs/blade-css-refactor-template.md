# Blade/CSS リファクタテンプレ

画面設計（DG01/DG02）準拠に向けた、共通テンプレート。

## 1. レイアウト共通（Blade）

```blade
@extends('layouts.app')

@section('title', '画面名')

@section('content')
<div class="ct-card">
  <h1 class="ct-title">画面名</h1>

  <p class="ct-muted">補足テキスト</p>

  <div class="ct-actions">
    <button class="ct-btn ct-btn--primary" type="button">主要操作</button>
    <button class="ct-btn" type="button">副操作</button>
  </div>
</div>
@endsection
```

## 2. フォームテンプレ

```blade
<form method="POST" action="{{ route('xxx') }}" class="ct-form">
  @csrf

  <div class="ct-field">
    <label class="ct-label" for="email">メールアドレス</label>
    <input class="ct-input" id="email" type="email" name="email" value="{{ old('email') }}">
    @error('email')
      <p class="ct-error">{{ $message }}</p>
    @enderror
  </div>

  <button class="ct-btn ct-btn--primary" type="submit">送信</button>
</form>
```

## 3. テーブルテンプレ

```blade
<div class="ct-table-wrap">
  <table class="ct-table">
    <thead>
      <tr>
        <th>列1</th>
        <th>列2</th>
        <th>列3</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>値1</td>
        <td>値2</td>
        <td>値3</td>
      </tr>
    </tbody>
  </table>
</div>
```

## 4. 置換ルール（推奨）

- `style="..."` は原則削除して `ct-*` クラスへ移行
- ボタンは `ct-btn` / `ct-btn--primary` に統一
- 入力は `ct-input` / `ct-label` / `ct-error` に統一
- ページの最大幅は `ct-container` に集約

