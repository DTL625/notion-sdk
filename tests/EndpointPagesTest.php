<?php

namespace DTL\NotionApi\Tests;

use Carbon\Carbon;
use DTL\NotionApi\Entities\Page;
use DTL\NotionApi\Entities\Properties\Checkbox;
use DTL\NotionApi\Entities\Properties\Date;
use DTL\NotionApi\Entities\Properties\Email;
use DTL\NotionApi\Entities\Properties\MultiSelect;
use DTL\NotionApi\Entities\Properties\Number;
use DTL\NotionApi\Entities\Properties\People;
use DTL\NotionApi\Entities\Properties\PhoneNumber;
use DTL\NotionApi\Entities\Properties\Relation;
use DTL\NotionApi\Entities\Properties\Select;
use DTL\NotionApi\Entities\Properties\Text;
use DTL\NotionApi\Entities\Properties\Url;
use DTL\NotionApi\Entities\PropertyItems\RichDate;
use DTL\NotionApi\Entities\PropertyItems\RichText;
use DTL\NotionApi\Entities\PropertyItems\SelectItem;
use DTL\NotionApi\Entities\User;
use DTL\NotionApi\Exceptions\NotionException;
use Illuminate\Support\Facades\Http;
use Notion;

/**
 * Class EndpointPageTest.
 *
 * The fake API responses are based on our test environment (since the current Notion examples do not match with the actual calls).
 *
 * @see https://developers.notion.com/reference/get-page
 */
class EndpointPagesTest extends NotionApiTest
{
    /** @test */
    public function it_throws_a_notion_exception_bad_request()
    {
        // failing /v1
        Http::fake([
            'https://api.notion.com/v1/pages*' => Http::response(
                json_decode('{}', true),
                400,
                ['Headers']
            ),
        ]);

        $this->expectException(NotionException::class);
        $this->expectExceptionMessage('Bad Request');

        Notion::pages()->find('afd5f6fb-1cbd-41d1-a108-a22ae0d9bac8');
    }

    /** @test */
    public function it_returns_page_entity_with_filled_properties()
    {
        // successful /v1/pages/PAGE_DOES_EXIST
        Http::fake([
            'https://api.notion.com/v1/pages/afd5f6fb-1cbd-41d1-a108-a22ae0d9bac8' => Http::response(
                json_decode(file_get_contents('tests/stubs/endpoints/pages/response_specific_200.json'), true),
                200,
                ['Headers']
            ),
        ]);

        $pageResult = Notion::pages()->find('afd5f6fb-1cbd-41d1-a108-a22ae0d9bac8');

        $this->assertInstanceOf(Page::class, $pageResult);

        // check properties
        $this->assertSame('Notion Is Awesome', $pageResult->getTitle());
        $this->assertSame('page', $pageResult->getObjectType());
        $this->assertCount(7, $pageResult->getRawProperties());
        $this->assertCount(7, $pageResult->getProperties());
        $this->assertCount(7, $pageResult->getPropertyKeys());

        $this->assertInstanceOf(Carbon::class, $pageResult->getCreatedTime());
        $this->assertInstanceOf(Carbon::class, $pageResult->getLastEditedTime());
    }

    /** @test */
    public function it_throws_a_notion_exception_not_found()
    {
        // failing /v1/pages/PAGE_DOES_NOT_EXIST
        Http::fake([
            'https://api.notion.com/v1/pages/b55c9c91-384d-452b-81db-d1ef79372b79' => Http::response(
                json_decode(file_get_contents('tests/stubs/endpoints/pages/response_specific_404.json'), true),
                200,
                ['Headers']
            ),
        ]);

        $this->expectException(NotionException::class);
        $this->expectExceptionMessage('Not found');

        Notion::pages()->find('b55c9c91-384d-452b-81db-d1ef79372b79');
    }

    /** @test */
    public function it_assembles_properties_for_a_new_page()
    {

        // test values
        $pageId = '0349b883a1c64539b435289ea62b6eab';
        $pageTitle = 'I was updated from Tinkerwell';

        $checkboxKey = 'CheckboxProperty';
        $checkboxValue = true;
        $dateRangeKey = 'DateRangeProperty';
        $dateRangeStartValue = Carbon::now()->toDateTime();
        $dateRangeEndValue = Carbon::tomorrow()->toDateTime();
        $dateKey = 'DateProperty';
        $dateValue = Carbon::yesterday()->toDateTime();
        $emailKey = 'EmailProperty';
        $emailValue = 'notion-is-awesome@example.org';
        $multiSelectKey = 'MultiSelectProperty';
        $multiSelectValues = ['Laravel', 'Notion'];
        $numberKey = 'NumberProperty';
        $numberValue = 42.42;
        $peopleKey = 'PeopleProperty';
        $peopleValue = ['04536682-603a-4531-a18f-4fa89fdfb4a8', '2fc9a200-e932-4428-baab-ba14526a139b'];
        $phoneKey = 'PhoneKey';
        $phoneValue = '999-888-777-666';
        $relationKey = 'RelationProperty';
        $relationValue = ['dc4ce5b0-0f51-4f6e-b130-5ac7a1b5101d'];
        $selectKey = 'SelectProperty';
        $selectValue = 'I choose you, Pikachu';
        $textKey = 'TextProperty';
        $textValue = "Isn't this awesome?";
        $urlKey = 'UrlProperty';
        $urlValue = 'https://5amco.de';

        // build the page with properties
        $page = new Page();
        $page->setId($pageId);
        $page->setTitle('Name', $pageTitle);
        $page->setCheckbox($checkboxKey, $checkboxValue);
        $page->setDate($dateRangeKey, $dateRangeStartValue, $dateRangeEndValue);
        $page->setDate($dateKey, $dateValue);
        $page->setEmail($emailKey, $emailValue);
        $page->setMultiSelect($multiSelectKey, $multiSelectValues);
        $page->setNumber($numberKey, $numberValue);
        $page->setPeople($peopleKey, $peopleValue);
        $page->setPhoneNumber($phoneKey, $phoneValue);
        $page->setRelation($relationKey, $relationValue);
        $page->setSelect($selectKey, $selectValue);
        $page->setText($textKey, $textValue);
        $page->setUrl($urlKey, $urlValue);

        // read the set properties
        $properties = $page->getProperties();

        // id, title
        $this->assertEquals($page->getId(), $pageId);
        $this->assertEquals($page->getTitle(), $pageTitle);

        // checkbox
        $this->assertTrue(
            $this->assertContainsInstanceOf(Checkbox::class, $properties)
        );
        $checkboxProp = $page->getProperty($checkboxKey);
        $this->assertEquals($checkboxKey, $checkboxProp->getTitle());
        $checkboxContent = $checkboxProp->getRawContent();
        $this->assertArrayHasKey('checkbox', $checkboxContent);
        $this->assertEquals($checkboxContent['checkbox'], $checkboxValue);
        $this->assertEquals($checkboxProp->getContent(), $checkboxValue);
        $this->assertEquals($checkboxProp->asText(), $checkboxValue ? 'true' : 'false');

        // date range
        $this->assertTrue(
            $this->assertContainsInstanceOf(Date::class, $properties)
        );
        $dateRangeProp = $page->getProperty($dateRangeKey);
        $this->assertInstanceOf(RichDate::class, $dateRangeProp->getContent());
        $dateRangeContent = $dateRangeProp->getContent();
        $this->assertTrue($dateRangeProp->isRange());
        $this->assertEquals($dateRangeStartValue, $dateRangeProp->getStart());
        $this->assertEquals($dateRangeEndValue, $dateRangeProp->getEnd());
        $this->assertJson($dateRangeProp->asText());
        $this->assertStringContainsString($dateRangeStartValue->format('Y-m-d H:i:s'), $dateRangeProp->asText());
        $this->assertStringContainsString($dateRangeEndValue->format('Y-m-d H:i:s'), $dateRangeProp->asText());
        $dateRangeContent = $dateRangeProp->getRawContent();
        $this->assertArrayHasKey('date', $dateRangeContent);
        $this->assertCount(2, $dateRangeContent['date']);
        $this->assertArrayHasKey('start', $dateRangeContent['date']);
        $this->assertEquals($dateRangeStartValue->format('c'), $dateRangeContent['date']['start']);
        $this->assertArrayHasKey('end', $dateRangeContent['date']);
        $this->assertEquals($dateRangeEndValue->format('c'), $dateRangeContent['date']['end']);

        // date
        $dateProp = $page->getProperty($dateKey);
        $this->assertInstanceOf(RichDate::class, $dateProp->getContent());
        $this->assertFalse($dateProp->isRange());
        $this->assertEquals($dateValue, $dateProp->getStart());
        $dateContent = $dateProp->getRawContent();
        $this->assertArrayHasKey('date', $dateContent);
        $this->assertCount(1, $dateContent['date']);
        $this->assertArrayHasKey('start', $dateContent['date']);
        $this->assertEquals($dateValue->format('c'), $dateContent['date']['start']);

        // email
        $this->assertTrue($this->assertContainsInstanceOf(Email::class, $properties));
        $mailProp = $page->getProperty($emailKey);
        $this->assertInstanceOf(Email::class, $mailProp);
        $this->assertEquals($emailValue, $mailProp->getContent());
        $this->assertEquals($emailValue, $mailProp->getEmail());
        $mailContent = $mailProp->getRawContent();
        $this->assertArrayHasKey('email', $mailContent);
        $this->assertEquals($mailContent['email'], $emailValue);

        // multi-select
        $this->assertTrue($this->assertContainsInstanceOf(MultiSelect::class, $properties));
        $multiSelectProp = $page->getProperty($multiSelectKey);
        $this->assertIsIterable($multiSelectProp->getContent());
        $this->assertContainsOnlyInstancesOf(SelectItem::class, $multiSelectProp->getContent());
        $this->assertEquals('Laravel', $multiSelectProp->getContent()->first()->getName());

        $multiSelectContent = $multiSelectProp->getRawContent();
        $this->assertArrayHasKey('multi_select', $multiSelectContent);
        $this->assertCount(2, $multiSelectContent['multi_select']);
        $this->assertIsIterable($multiSelectContent['multi_select'][0]);
        $this->assertArrayHasKey('name', $multiSelectContent['multi_select'][0]);
        $this->assertEquals('Laravel', $multiSelectContent['multi_select'][0]['name']);
        $this->assertIsIterable($multiSelectContent['multi_select'][1]);
        $this->assertArrayHasKey('name', $multiSelectContent['multi_select'][1]);
        $this->assertEquals('Notion', $multiSelectContent['multi_select'][1]['name']);

        // number
        $this->assertTrue($this->assertContainsInstanceOf(Number::class, $properties));
        $numberProp = $page->getProperty($numberKey);
        $this->assertEquals($numberValue, $numberProp->getContent());
        $this->assertEquals($numberValue, $numberProp->getNumber());
        $numberContent = $numberProp->getRawContent();
        $this->assertArrayHasKey('number', $numberContent);
        $this->assertEquals($numberContent['number'], $numberValue);

        // people
        $this->assertTrue($this->assertContainsInstanceOf(People::class, $properties));
        $peopleProp = $page->getProperty($peopleKey);
        $this->assertEquals($peopleProp->getContent(), $peopleProp->getPeople());
        $this->assertCount(2, $peopleProp->getPeople());
        $this->assertContainsOnlyInstancesOf(User::class, $peopleProp->getPeople());
        $this->assertEquals($peopleValue[0], $peopleProp->getPeople()->first()->getId());
        $peopleContent = $peopleProp->getRawContent();
        $this->assertArrayHasKey('people', $peopleContent);
        $this->assertArrayHasKey('object', $peopleContent['people'][0]);
        $this->assertArrayHasKey('id', $peopleContent['people'][0]);
        $this->assertEquals($peopleContent['people'][0]['object'], 'user');
        $this->assertEquals($peopleContent['people'][0]['id'], $peopleValue[0]);
        $this->assertArrayHasKey('object', $peopleContent['people'][1]);
        $this->assertArrayHasKey('id', $peopleContent['people'][1]);
        $this->assertEquals('user', $peopleContent['people'][1]['object']);
        $this->assertEquals($peopleValue[1], $peopleContent['people'][1]['id']);

        // phone number
        $this->assertTrue($this->assertContainsInstanceOf(PhoneNumber::class, $properties));
        $phoneProp = $page->getProperty($phoneKey);
        $this->assertEquals($phoneValue, $phoneProp->getPhoneNumber());
        $this->assertEquals($phoneProp->getContent(), $phoneProp->getPhoneNumber());
        $phoneContent = $phoneProp->getRawContent();
        $this->assertArrayHasKey('phone_number', $phoneContent);
        $this->assertEquals($phoneContent['phone_number'], $phoneValue);

        // relation
        $this->assertTrue($this->assertContainsInstanceOf(Relation::class, $properties));
        $relationProp = $page->getProperty($relationKey);
        $this->assertEquals($relationValue[0], $relationProp->getRelation()->first());
        $this->assertEquals($relationProp->getContent(), $relationProp->getRelation());
        $relationContent = $relationProp->getRawContent();
        $this->assertArrayHasKey('relation', $relationContent);
        $this->assertArrayHasKey('id', $relationContent['relation'][0]);
        $this->assertEquals($relationValue[0], $relationContent['relation'][0]['id']);

        // select
        $this->assertTrue($this->assertContainsInstanceOf(Select::class, $properties));
        $selectProp = $page->getProperty($selectKey);
        $this->assertInstanceOf(SelectItem::class, $selectProp->getContent());
        $this->assertEquals($selectValue, $selectProp->getContent()->getName());
        $selectContent = $selectProp->getRawContent();
        $this->assertArrayHasKey('select', $selectContent);
        $this->assertArrayHasKey('name', $selectContent['select']);
        $this->assertEquals($selectValue, $selectContent['select']['name']);

        // text
        $this->assertTrue($this->assertContainsInstanceOf(Text::class, $properties));
        $textProp = $page->getProperty($textKey);
        $this->assertInstanceOf(RichText::class, $textProp->getContent());
        $this->assertEquals($textValue, $textProp->getContent()->getPlainText());
        $textContent = $textProp->getRawContent();
        $this->assertArrayHasKey('rich_text', $textContent);
        $this->assertCount(1, $textContent['rich_text']);
        $this->assertArrayHasKey('type', $textContent['rich_text'][0]);
        $this->assertArrayHasKey('text', $textContent['rich_text'][0]);
        $this->assertEquals('text', $textContent['rich_text'][0]['type']);
        $this->assertArrayHasKey('content', $textContent['rich_text'][0]['text']);
        $this->assertEquals($textValue, $textContent['rich_text'][0]['text']['content']);

        // url
        $this->assertTrue($this->assertContainsInstanceOf(Url::class, $properties));
        $urlProp = $page->getProperty($urlKey);
        $this->assertEquals($urlValue, $urlProp->getUrl());
        $this->assertEquals($urlProp->getContent(), $urlProp->getUrl());
        $urlContent = $urlProp->getRawContent();
        $this->assertArrayHasKey('url', $urlContent);
        $this->assertEquals($urlValue, $urlContent['url']);
    }
}
