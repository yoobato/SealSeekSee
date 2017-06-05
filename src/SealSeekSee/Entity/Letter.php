<?php
namespace SealSeekSee\Entity;

// TODO: 편지에 사진도 추가할 수 있게 할까? DB에 letter_attach 이런 테이블 추가해야 할듯

/**
 * @Entity @Table(name="letter")
 */
class Letter
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="IDENTITY")
     */
    public $id;

    /**
     * @Column(type="string")
     */
    public $sender_phone;

    /**
     * @Column(type="string")
     */
    public $receiver_phone;

    /**
     * @Column(type="string")
     */
    public $title;

    /**
     * @Column(type="string")
     */
    public $message;

    /**
     * @Column(type="string")
     */
    public $w3w_address;

    /**
     * @Column(type="decimal")
     */
    public $latitude;

    /**
     * @Column(type="decimal")
     */
    public $longitude;

    /**
     * @Column(type="string")
     */
    public $created_date;

    public function __construct($sender_phone, $receiver_phone, $title, $message, $w3w_address, $latitude, $longitude)
    {
        $this->sender_phone = $sender_phone;
        $this->receiver_phone = $receiver_phone;
        $this->title = $title;
        $this->message = $message;
        $this->w3w_address = $w3w_address;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }
}
