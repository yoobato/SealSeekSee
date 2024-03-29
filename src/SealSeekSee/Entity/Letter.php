<?php
namespace SealSeekSee\Entity;

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

    /**
     * @Column(type="string")
     */
    public $opened_date;

    public function __construct($sender_phone, $receiver_phone, $title, $message, $w3w_address, $latitude, $longitude)
    {
        $this->sender_phone = $sender_phone;
        $this->receiver_phone = $receiver_phone;
        $this->title = $title;
        $this->message = $message;
        $this->w3w_address = $w3w_address;
        $this->latitude = $latitude;
        $this->longitude = $longitude;

        $this->created_date = date('Y-m-d H:i:s');
    }

    public function exportMetadataDTO()
    {
        $dto = new \stdClass();
        $dto->id = $this->id;
        $dto->latitude = $this->latitude;
        $dto->longitude = $this->longitude;
        return $dto;
    }
}
