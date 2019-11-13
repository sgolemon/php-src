--TEST--
Usages of the nullsafe operator inside foreach loop
--FILE--
<?php

function action(?Token $token = null): void {

  foreach($token?->getUser()?->articles ?? [] as $article) {
    printf('- %s%s', $article?->category ?? 'default', PHP_EOL);
  }

  printf('OK%s', PHP_EOL);
}

final class Token {
  public ?User $user = null;

  public function getUser(): ?User {
    return $this->user;
  }
}

final class User {
  public ?array $articles = null;
}

final class Article {
  public ?string $category;
  public function __construct(?string $category) {
    $this->category = $category;
  }
}

action(null);
action(new Token());
action(new Token(new User()));

$user = new User();
$user->articles = [
  new Article('Foo'),
  new Article('Bar'),
];

$token = new Token();
$token->user = $user;

action($token);
$user->article = [
  new Article('Foo'),
  new Article(null),
  new Article('Bar'),
]
action($token);

printf('-- DONE --');
--EXPECTF--
OK
OK
OK
- Foo
- Bar
OK
- Foo
- default
- Bar
OK
-- DONE --
